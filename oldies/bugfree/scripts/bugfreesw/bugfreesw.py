from jira.client import JIRA
import datetime
import subprocess
import os.path
import smtplib
from dateutil import parser
from email.mime.text import MIMEText
now = datetime.datetime.now()

import yaml
if os.path.isfile('../../service/config/charts.yml') == True:
    configuration = yaml.safe_load(open("../../service/config/charts.yml"))
else:
    configuration = yaml.safe_load(open("../../config/charts.yml"))

jira_server = configuration['Server name']
jira_user = configuration['Login name']
jira_password = configuration['Password']

jira_server = {'server': jira_server}
jira = JIRA(options=jira_server, basic_auth=(jira_user, jira_password))

today_date = datetime.datetime.now().strftime("%Y-%m-%d")

filter01 = 'labels = BugFreeSW-2017Q4 AND Complexity = L AND status != Closed'
filter02 = 'labels = BugFreeSW-2017Q4 AND Complexity = L AND status = Closed'
filter03 = 'labels = BugFreeSW-2017Q4 AND Complexity = M AND status != Closed'
filter04 = 'labels = BugFreeSW-2017Q4 AND Complexity = M AND status = Closed'
filter05 = 'labels = BugFreeSW-2017Q4 AND Complexity = S AND status != Closed'
filter06 = 'labels = BugFreeSW-2017Q4 AND Complexity = S AND status = Closed'

Total_cost = int(jira.search_issues(filter01, maxResults=1000).total)*10000+int(jira.search_issues(filter02, maxResults=1000).total)*10000+int(jira.search_issues(filter03, maxResults=1000).total)*5000+int(jira.search_issues(filter04, maxResults=1000).total)*5000+int(jira.search_issues(filter05, maxResults=1000).total)*2500+int(jira.search_issues(filter06, maxResults=1000).total) * 2500
Balance = 100000 - Total_cost

if os.path.isfile('../../data/bugfreesw_stats.txt') == True:
    with open('../../data/bugfreesw_stats.txt', 'a') as file:
        file.write(today_date+
        ','+str(jira.search_issues(filter01, maxResults=1000).total)+','+str(jira.search_issues(filter02, maxResults=1000).total)+','+str(jira.search_issues(filter03, maxResults=1000).total)+','+str(jira.search_issues(filter04, maxResults=1000).total)+','+str(jira.search_issues(filter05, maxResults=1000).total)+','+str(jira.search_issues(filter06, maxResults=1000).total)+','+str(Total_cost)+','+str(Balance)+'\n')
else:
    with open('../../data/bugfreesw_stats.txt', 'w') as file:
        file.write('Date,"L - Opened","L - Closed","M - Opened","M - Closed","S - Opened","S - Closed","Total cost","Balance"\n')
        file.write(today_date+','+str(jira.search_issues(filter01, maxResults=1000).total)+','+str(jira.search_issues(filter02, maxResults=1000).total)+','+str(jira.search_issues(filter03, maxResults=1000).total)+','+str(jira.search_issues(filter04, maxResults=1000).total)+','+str(jira.search_issues(filter05, maxResults=1000).total)+','+str(jira.search_issues(filter06, maxResults=1000).total)+','+str(Total_cost)+','+str(Balance)+'\n')

sender = 'alexey.levashev@zoomint.com'
receivers = ['alexey.levashev@zoomint.com']

message = """"From: Robot <alexey.levashev@zoomint.com>
To: To Person <alexey.levashev@zoomint.com>
Subject: BugFree Software Information Email
Total cost of the bugs in progress and closed is : """ + str(Total_cost) + """
Money to spent : """ + str(Balance) + """
"""

if Balance <= 10000:
    try:
       smtpObj = smtplib.SMTP('localhost')
       smtpObj.sendmail(sender, receivers, message)
    except SMTPException:
       print "Error: unable to send email"
