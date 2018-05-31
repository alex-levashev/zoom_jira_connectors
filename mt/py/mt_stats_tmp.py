#!/usr/bin/python
import MySQLdb
from jira.client import JIRA
import datetime
import subprocess
import os.path
from dateutil import parser
now = datetime.datetime.now()
import json
import subprocess

def parse_php_assignments(php):
	reg = r'\$(?P<variable>\w+)\s*=\s*"?\'?(?P<value>[^"\';]+)"?\'?;'
	rg = re.compile(reg,re.IGNORECASE|re.DOTALL)
	return rg.findall(php)

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

db = MySQLdb.connect(host="localhost", user="root", passwd="Alex123", db="mt")

def MembersByTeam(team_name):
    subprocess.call(["php", "/var/www/html/mt/int/teams_rates_print.php"])
    with open('/var/www/html/mt/int/teams_rates.json', encoding='utf-8') as data_file:
        data = json.loads(data_file.read())
    team = []
    for key in data:
        if(data[key]['team'] == team_name):
            team.append(str(key))
    return(team)



team = MembersByTeam('Pilgrim')
print(team)
