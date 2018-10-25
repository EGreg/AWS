# AWS

Aws are managed used to create a domain and sub-domain dynamically with Qbix script. It is managed to our spam mail using functionality.

## Getting Started

Install new Qbix clone with a new name and do all the process for Qbix installation for reference site https://qbix.com/platform/guide/installing

### Prerequisites

Add your Credentials in Platform/your_app_name/local/app.json 

```
"apps": {
			
			"AWS": {
				"Aws_App": 
				{
					"accessKey" : "AWS IAM AccessKey",
					"secretKey" : "AWS IAM SecretKey",
					"host_ses" : "AWS SES host",
					"host_route53" : "AWS route53 host",
					"domain" : "AWS Domain Name",
					"MailToDomain" : "AWS Mail Domain Name"
				}
			},
```

## Running the tests

After this process you can run following command :

```
cd Platform/your_app_name/scripts/Q php aws.php
```

###Testing

After running below command the following step are run

1) Host Zone Created in AWS Route53.
2) Verify Domain Created in AWS SES.
3) Get TXT Record to AWS SES and Added in Route 53 as TXT Record.
4) Set Sub-Domain as the Mail server in AWS SES.
5) Get MX Record to AWS SES and Added in Route 53 as MX Record.
6) Get SPF Record to AWS SES and Added in Route 53 as SPF Record.


### For More information 

see this video https://www.useloom.com/share/016cad963cfd443d80d993ec56702d71