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

filter01 = 'fixVersion = ZOOM-6.4.0 AND issuetype in (Story, Bug, "Technical Task") AND labels in (Team:UA, Team:ENC, Team:TheBazaar, scopic, devops)'


def time_count_per_filter( filter ):
    issues = jira.search_issues(filter, maxResults=1000)
    issues_total_hours = 0
    for issue in issues:
        if (str(issue.fields.customfield_11506) == "S") & ("Team:UA" in issue.fields.labels):
            issues_total_hours += 3
        if (str(issue.fields.customfield_11506) == "S") & ("Team:ENC" in issue.fields.labels):
            issues_total_hours += 2
        if (str(issue.fields.customfield_11506) == "S") & ("Team:TheBazaar" in issue.fields.labels):
            issues_total_hours += 2
        if (str(issue.fields.customfield_11506) == "S") & ("scopic" in issue.fields.labels):
            issues_total_hours += 2
        if (str(issue.fields.customfield_11506) == "S") & ("devops" in issue.fields.labels):
            issues_total_hours += 5

        if (str(issue.fields.customfield_11506) == "M") & ("Team:UA" in issue.fields.labels):
            issues_total_hours += 10
        if (str(issue.fields.customfield_11506) == "M") & ("Team:ENC" in issue.fields.labels):
            issues_total_hours += 5
        if (str(issue.fields.customfield_11506) == "M") & ("Team:TheBazaar" in issue.fields.labels):
            issues_total_hours += 5
        if (str(issue.fields.customfield_11506) == "M") & ("scopic" in issue.fields.labels):
            issues_total_hours += 7
        if (str(issue.fields.customfield_11506) == "M") & ("devops" in issue.fields.labels):
            issues_total_hours += 9

        if (str(issue.fields.customfield_11506) == "L") & ("Team:UA" in issue.fields.labels):
            issues_total_hours += 20
        if (str(issue.fields.customfield_11506) == "L") & ("Team:ENC" in issue.fields.labels):
            issues_total_hours += 10
        if (str(issue.fields.customfield_11506) == "L") & ("Team:TheBazaar" in issue.fields.labels):
            issues_total_hours += 10
        if (str(issue.fields.customfield_11506) == "L") & ("scopic" in issue.fields.labels):
            issues_total_hours += 14
        if (str(issue.fields.customfield_11506) == "L") & ("devops" in issue.fields.labels):
            issues_total_hours += 13

        if (str(issue.fields.customfield_11506) == "XL") & ("Team:UA" in issue.fields.labels):
            issues_total_hours += 45
        if (str(issue.fields.customfield_11506) == "XL") & ("Team:ENC" in issue.fields.labels):
            issues_total_hours += 20
        if (str(issue.fields.customfield_11506) == "XL") & ("Team:TheBazaar" in issue.fields.labels):
            issues_total_hours += 20
        if (str(issue.fields.customfield_11506) == "XL") & ("scopic" in issue.fields.labels):
            issues_total_hours += 30
        if (str(issue.fields.customfield_11506) == "XL") & ("devops" in issue.fields.labels):
            issues_total_hours += 24


        if (str(issue.fields.customfield_11506) == "XXL") & ("Team:UA" in issue.fields.labels):
            issues_total_hours += 100
        if (str(issue.fields.customfield_11506) == "XXL") & ("Team:ENC" in issue.fields.labels):
            issues_total_hours += 50
        if (str(issue.fields.customfield_11506) == "XXL") & ("Team:TheBazaar" in issue.fields.labels):
            issues_total_hours += 80
        if (str(issue.fields.customfield_11506) == "XXL") & ("scopic" in issue.fields.labels):
            issues_total_hours += 90
        if (str(issue.fields.customfield_11506) == "XXL") & ("devops" in issue.fields.labels):
            issues_total_hours += 56

    return issues_total_hours

if os.path.isfile('../data/burndown.txt') == True:
    with open('../data/burndown.txt', 'a') as file:
        file.write(today_date+',' + str(time_count_per_filter(filter01)) + '\n')
else:
    with open('../data/burndown.txt', 'w') as file:
        file.write('Date,Count\n')
        file.write(today_date+',' + str(time_count_per_filter(filter01)) + '\n')
