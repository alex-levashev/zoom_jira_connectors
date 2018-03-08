from jira.client import JIRA
import datetime
import subprocess
import os.path
from dateutil import parser
now = datetime.datetime.now()

import yaml
if os.path.isfile('../service/config/charts.yml') == True:
    configuration = yaml.safe_load(open("../service/config/charts.yml"))
else:
    configuration = yaml.safe_load(open("../config/charts.yml"))

jira_server = configuration['Server name']
jira_user = configuration['Login name']
jira_password = configuration['Password']

jira_server = {'server': jira_server}
jira = JIRA(options=jira_server, basic_auth=(jira_user, jira_password))

today_date = datetime.datetime.now().strftime("%Y-%m-%d")

############# FILTER01 #############

filter01 = '(issuetype = Bug AND status not in (Closed) AND "Discovered During" = customer AND "Epic Link" IS EMPTY AND ("Design Issue" !=Yes OR "Design Issue" is EMPTY) AND (labels not in ("ZOOMonZOOM") or labels is EMPTY)) OR ((issuetype not in (Epic, Story,Bug) AND status not in (Closed) AND "Discovered During" = customer AND "Epic Link" IS EMPTY AND (labels not in ("ZOOMonZOOM") or labels is EMPTY)))'
filter02 = '"Discovered During" = customer AND status = "In Test"'

def time_count_per_filter( filter ):
    issues = jira.search_issues(filter, maxResults=1000)
    issues_total_hours = 0
    for issue in issues:
        print issue, issue.fields.customfield_11506
        if str(issue.fields.customfield_11506) == "S":
            issues_total_hours += 2
        if str(issue.fields.customfield_11506) == "M":
            issues_total_hours += 7
        if str(issue.fields.customfield_11506) == "L":
            issues_total_hours += 14
        if str(issue.fields.customfield_11506) == "XL":
            issues_total_hours += 30
        if str(issue.fields.customfield_11506) == "XXL":
            issues_total_hours += 70
    # print issues_total_hours
    return issues_total_hours

xx = time_count_per_filter(filter02)
print xx


# for field_name in issues:
#     print "Field:", field_name, "Value:", field_name.__dict__;


# if os.path.isfile('../data/filter01.txt') == True:
#     with open('../data/filter01.txt', 'a') as file:
#         file.write(today_date+','+str(jira.search_issues(filter01, maxResults=1000).total)+'\n')
# else:
#     with open('../data/filter01.txt', 'w') as file:
#         file.write('Date,"Issues"\n')
#         file.write(today_date+','+str(jira.search_issues(filter01, maxResults=1000).total)+'\n')

############# END OF FILTER01 #############
