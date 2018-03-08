from jira.client import JIRA
import datetime
import subprocess
import os.path
from dateutil import parser
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

filter01 = 'issuetype not in (Epic, Story) AND status not in (Closed) AND "Discovered During" = customer AND "Epic Link" is EMPTY AND "Design Issue" != Yes AND labels not in (ZOOMonZOOM) OR (issuetype in ("Technical Task", "Info Request") AND status != Closed AND "Discovered During" = customer AND "Epic Link" is EMPTY AND labels not in (ZOOMonZOOM))'
filter02 = 'issuetype not in (Epic, Story) AND status not in (Closed, Accepted) AND "Discovered During" = customer'
filter03 = 'issuetype not in (Epic, Story) AND status not in (Closed, Accepted) AND "Discovered During" = customer AND "Design Issue" = Yes'
filter04 = 'issuetype not in (Epic, Story) AND status not in (Closed, Accepted) AND "Discovered During" = customer AND ("Design Issue" != YES OR "Design Issue" = EMPTY) AND labels = ZOOMonZOOM'
filter05 = '"Discovered During" = customer AND createdDate >= -6d'

if os.path.isfile('../../data/mt_stats.txt') == True:
    with open('../../data/mt_stats.txt', 'a') as file:
        file.write(today_date+','+str(jira.search_issues(filter01, maxResults=1000).total)+','+str(jira.search_issues(filter02, maxResults=1000).total)+','+str(jira.search_issues(filter03, maxResults=1000).total)+','+str(jira.search_issues(filter04, maxResults=1000).total)+','+str(jira.search_issues(filter05, maxResults=1000).total)+'\n')
else:
    with open('../../data/mt_stats.txt', 'w') as file:
        file.write('Date,"All Issues","Everything","Design Issues","ZOOM-on-ZOOM","Issues - Last week"\n')
        file.write(today_date+','+str(jira.search_issues(filter01, maxResults=1000).total)+','+str(jira.search_issues(filter02, maxResults=1000).total)+','+str(jira.search_issues(filter03, maxResults=1000).total)+','+str(jira.search_issues(filter04, maxResults=1000).total)+','+str(jira.search_issues(filter05, maxResults=1000).total)+'\n')

bashCommand = "gnuplot ../../scripts/mt_stats/mt_stats_gnu.txt"
process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
output, error = process.communicate()

filter06 = 'status changed to Closed during (-6d, now()) AND status = Closed AND "Discovered During" = customer'

if os.path.isfile('../../data/mt_stats_inout.txt') == True:
    with open('../../data/mt_stats_inout.txt', 'a') as file:
        file.write(today_date+','+str(jira.search_issues(filter05, maxResults=1000).total)+','+str(jira.search_issues(filter06, maxResults=1000).total)+'\n')
else:
    with open('../../data/mt_stats_inout.txt', 'w') as file:
        file.write('Date,"Issue in - Last week","Issues out - Last week"\n')
        file.write(today_date+','+str(jira.search_issues(filter05, maxResults=1000).total)+','+str(jira.search_issues(filter06, maxResults=1000).total)+'\n')

bashCommand = "gnuplot ../../scripts/mt_stats/mt_stats_gnu_inout.txt"
process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
output, error = process.communicate()
