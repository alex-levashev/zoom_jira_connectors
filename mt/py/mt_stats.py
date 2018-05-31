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

### TEAM MEMBER EXTRACT PROCEDURE

def MembersByTeam(team_name):
    subprocess.call(["php", "/var/www/html/mt/int/teams_rates_print.php"])
    with open('/var/www/html/mt/int/teams_rates.json', encoding='utf-8') as data_file:
        data = json.loads(data_file.read())
    team = []
    for key in data:
        if(data[key]['team'] == team_name):
            team.append(str(key))
    return(team)



#### MT STATS START

today_date = datetime.datetime.now().strftime("%Y-%m-%d")
# today_date = '2018-05-19'

filter_mt_stats = '(issuetype = Bug AND status not in (Closed) AND "Discovered During" = customer AND "Epic Link" IS EMPTY AND ("Design Issue" !=Yes OR "Design Issue" is EMPTY) AND (labels not in ("ZOOMonZOOM") or labels is EMPTY)) OR (issuetype not in (Epic, Story,Bug) AND status not in (Closed) AND "Discovered During" = customer AND "Epic Link" IS EMPTY AND (labels not in ("ZOOMonZOOM") or labels is EMPTY))'

cursor = db.cursor()

# Check if table exists
cursor.execute('SHOW TABLES LIKE "MT_Daily"')
result = cursor.fetchone()
if result is None:
    cursor.execute('CREATE TABLE MT_Daily (CheckDate varchar(10) not null, Count1 int not null )')
cursor.execute('INSERT INTO MT_Daily (CheckDate, Count1) VALUES ("' + str(today_date) + '", "' + str(jira.search_issues(filter_mt_stats, maxResults=1000).total) + '")')
db.commit()


#### MT STATS END



#### MT STATS LUMIERE PRO PILGRIM START
team_pilgrim_members = MembersByTeam('Pilgrim')
team_lumierepro_members = MembersByTeam('LumierePro')
for iterate_days in range(1, 21):
    review_date = (datetime.datetime.now()-datetime.timedelta(days=iterate_days)).strftime("%Y-%m-%d")

    filter_pilgrim_lumiere_time_stats = 'worklogDate >= '+review_date+' AND worklogDate <= '+review_date

    totaltime_team_pilgrim = 0
    totaltime_team_lumierepro = 0
    for i in jira.search_issues(filter_pilgrim_lumiere_time_stats, maxResults=1000):
        worklogs = jira.worklogs(i.key)
        for team_pilgrim_member in team_pilgrim_members:
            for worklog in worklogs:
                if worklog.author.name == team_pilgrim_member and review_date in worklog.started:
                    totaltime_team_pilgrim += worklog.timeSpentSeconds
        for team_lumierepro_member in team_lumierepro_members:
            for worklog in worklogs:
                if worklog.author.name == team_lumierepro_member and review_date in worklog.started:
                    totaltime_team_lumierepro += worklog.timeSpentSeconds


    # Check if table exists
    cursor.execute('SHOW TABLES LIKE "LumierePro_Pilgrim_Stats"')
    result = cursor.fetchone()
    if result is None:
        cursor.execute('CREATE TABLE LumierePro_Pilgrim_Stats (CheckDate varchar(10) not null, Count1 int not null, Count2 int not null, UNIQUE (CheckDate))')
    cursor.execute('INSERT INTO LumierePro_Pilgrim_Stats (CheckDate, Count1, Count2) VALUES ("' + str(review_date) + '", "' + str(totaltime_team_pilgrim/3600)+ '", "' + str(totaltime_team_lumierepro/3600) + '") ON DUPLICATE KEY UPDATE Count1="' + str(totaltime_team_pilgrim/3600)+ '", Count2="' + str(totaltime_team_lumierepro/3600) + '"')

    db.commit()
    #### MT STATS LUMIERE PRO PILGRIM

#### IN OUT STATS START
filter_in = '"Discovered During" = customer AND created >= startOfDay(-1) AND created <= endOfDay(-1)'
filter_out = '"Discovered During" = customer AND resolved >= startOfDay(-1) AND resolved <= endOfDay(-1)'
# Check if table exists
cursor.execute('SHOW TABLES LIKE "InOutStats"')
result = cursor.fetchone()
if result is None:
    cursor.execute('CREATE TABLE InOutStats (CheckDate varchar(10) not null, Count1 int not null, Count2 int not null)')
cursor.execute('INSERT INTO InOutStats (CheckDate, Count1, Count2) VALUES ("' + str(review_date) + '", "' + str(jira.search_issues(filter_in, maxResults=1000).total)+ '", "' + str(jira.search_issues(filter_out, maxResults=1000).total) + '")')
db.commit()


#### IN OUT STATS END


db.close()

#### MT STATS LUMIERE PRO PILGRIM START
