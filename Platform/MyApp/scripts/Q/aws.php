#!/usr/bin/env php
<?php
$response = new STDClass;

$Q_Bootstrap_config_plugin_limit = 1;
include dirname(__FILE__).'/../Q.inc.php';

require_once('Route53.php');
require_once('SimpleEmailService.php');

$app = Q_Config::expect('Users', 'apps');
$app_name=Q::app();
$aws = $app['AWS'][$app_name];

$accessKey = $aws['accessKey'];
$secretKey = $aws['secretKey'];
$host_rout53 = $aws['host_route53'];
$host_ses = $aws['host_ses'];
$domain = $aws['domain'];
$mailTodomain = $aws['MailToDomain'];

$r53 = new Route53($accessKey, $secretKey,$host_rout53);
$ses = new SimpleEmailService($accessKey, $secretKey,$host_ses);

$date = gmdate('D, d M Y H:i:s e+5.30');

$create_hostzone_result=$r53->createHostedZone($domain,$date);

if(isset($create_hostzone_result['error']))
{
    echo $create_hostzone_result['error'];
    exit;
}
else
{
	echo "Host Zone Created.\n";
    $ZoneId=$create_hostzone_result['HostedZone']['Id'];
    $verify_domain_result = $ses->verifyDomainIdentity($domain);

    if(isset($verify_domain_result['error']))
	{
		echo $verify_domain_result['error'];
		exit;
	}
	else
	{
		echo "Verify Domain Created.\n";
		$Txt_value=$verify_domain_result->VerifyDomainIdentityResult->VerificationToken[0];
		
		$Txt_name='_amazonses.'.$domain;
		$changes=$r53->prepareChange('UPSERT',$Txt_name,'TXT','1800',[0 =>$Txt_value]);
		$create_TXT_record=$r53->changeResourceRecordSets($ZoneId,$changes);

		if(isset($create_TXT_record['error']))
		{
		   	echo $create_TXT_record['error'];
		    exit;
		}
		else
		{
			echo "TXT Record Added in Route 53.\n";
		    $email=$mailTodomain.".".$domain;
			$set_Mail = $ses->setIdentityMailFromDomain($domain,$email);

			if(isset($set_Mail['error']))
			{
			    echo $set_Mail['error'];
			    exit;
			}
			else
			{
				echo "Set Mail To Domain.\n";
			    $changes=$r53->prepareChange('UPSERT',$email,'MX','300',[0 =>'10 feedback-smtp.us-west-2.amazonses.com']);
				$create_MX_record=$r53->changeResourceRecordSets($ZoneId,$changes);
				if(isset($create_MX_record['error']))
				{
				    echo $create_MX_record['error'];
				    exit;
				}
				else
				{
					echo "MX Record Added in Route 53.\n";
					$changes=$r53->prepareChange('UPSERT',$email,'SPF','300',[0 =>'v=spf1 include:amazonses.com ~all']);

					$create_SPF_record=$r53->changeResourceRecordSets($ZoneId,$changes);
					if(isset($create_SPF_record['error']))
					{
					    echo $create_SPF_record['error'];
					    exit;
					}
					else
					{
						echo "SPF Record Added in Route 53.\n";
					}
				}
			}
		}
	}
}