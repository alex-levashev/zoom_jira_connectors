#!/usr/bin/env python
from jira.client import JIRA
import datetime
import time
import calendar
from dateutil import parser
now = datetime.datetime.now()

jira_server = "http://jira:81"
jira_user = ""
jira_password = ""

jira_server = {'server': jira_server}
jira = JIRA(options=jira_server, basic_auth=(jira_user, jira_password))

team_members_rates_scopic = {'zhuk': 22, 'bilyk': 22, 'chermyaninov': 22,'ugrinovic': 22, 'scopic.mjerotijevic': 18.5, 'lukyanchik': 22, 'Fonseca': 22, 'sikhvart': 22, 'vucic': 22}
team_members_rates_blueberry = {'blueberry.mlauko': 1250, 'blueberry.obartas': 1250}
team_members_rates_pilgrim =   {'pilgrim.akozhemyakin': 45, 'pilgrim.ashalbetsk': 45}
team_members_rates_lumiere = { 'lumiere.akapelonis': 1100, 'lumiere.ppribyl': 1100, 'lumiere.vli': 1100, 'ext.gbolyuba': 1100 }

current_month = int(now.strftime("%m"))
previous_month = current_month - 1

start_date = '2017-'+str(previous_month)+'-01 00:00:00+02:00'
end_date = '2017-'+str(current_month)+'-01 00:00:00+02:00'

total_cost = 0
total_hours = 0

filter_number = 'worklogDate >= '+parser.parse(start_date).strftime("%Y-%m-%d")+' AND worklogDate < '+parser.parse(end_date).strftime("%Y-%m-%d")
filter_array = jira.search_issues(filter_number, maxResults=1000)

team_members_rates = team_members_rates_scopic
team_members_hours = {'zhuk': 0, 'bilyk': 0, 'chermyaninov': 0,'ugrinovic': 0, 'scopic.mjerotijevic': 0, 'lukyanchik': 0, 'Fonseca': 0, 'sikhvart': 0, 'vucic': 0}
team_members_cost = {'zhuk': 0, 'bilyk': 0, 'chermyaninov': 0,'ugrinovic': 0, 'scopic.mjerotijevic': 0, 'lukyanchik': 0, 'Fonseca': 0, 'sikhvart': 0, 'vucic': 0}

for i in filter_array:
	worklogs = jira.worklogs(i.key)
	for team_member, rate in team_members_rates.items():
		for worklog in worklogs:
			if (worklog.author.name == team_member and (parser.parse(worklog.started)) <= parser.parse(end_date) and (parser.parse(worklog.started)) >= parser.parse(start_date)):
				team_members_hours[worklog.author.name] += (float(worklog.timeSpentSeconds)/3600)
				team_members_cost[worklog.author.name] += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_cost += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_hours += (float(worklog.timeSpentSeconds)/3600)
				print total_hours

print '---------------'
print 'Scopic - Total cost - ',total_cost,'USD'
print 'Scopic - Total hours -',total_hours,'hours'
print 'By hours -',team_members_hours
print 'By cost -',team_members_cost
print '---------------'

total_cost = 0
total_hours = 0

team_members_rates = team_members_rates_blueberry
team_members_hours = {'blueberry.mlauko': 0, 'blueberry.obartas': 0}
team_members_cost = {'blueberry.mlauko': 0, 'blueberry.obartas': 0}

for i in filter_array:
	worklogs = jira.worklogs(i.key)
	for team_member, rate in team_members_rates.items():
		for worklog in worklogs:
			if (worklog.author.name == team_member and (parser.parse(worklog.started)) <= parser.parse(end_date) and (parser.parse(worklog.started)) >= parser.parse(start_date)):
				team_members_hours[worklog.author.name] += (float(worklog.timeSpentSeconds)/3600)
				team_members_cost[worklog.author.name] += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_cost += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_hours += (float(worklog.timeSpentSeconds)/3600)

print 'Blueberry - Total cost - ',total_cost,'CZK'
print 'Blueberry - Total hours -',total_hours,'hours'
print 'By hours -',team_members_hours
print 'By cost -',team_members_cost
print '---------------'

total_cost = 0
total_hours = 0

team_members_rates = team_members_rates_pilgrim
team_members_hours = {'pilgrim.akozhemyakin': 0, 'pilgrim.ashalbetsk': 0}
team_members_cost = {'pilgrim.akozhemyakin': 0, 'pilgrim.ashalbetsk': 0}

for i in filter_array:
	worklogs = jira.worklogs(i.key)
	for team_member, rate in team_members_rates.items():
		for worklog in worklogs:
			if (worklog.author.name == team_member and (parser.parse(worklog.started)) <= parser.parse(end_date) and (parser.parse(worklog.started)) >= parser.parse(start_date)):
				team_members_hours[worklog.author.name] += (float(worklog.timeSpentSeconds)/3600)
				team_members_cost[worklog.author.name] += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_cost += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_hours += (float(worklog.timeSpentSeconds)/3600)

print 'Pilgrim - Total cost - ',total_cost,'USD'
print 'Pilgrim - Total hours -',total_hours,'hours'
print 'By hours -',team_members_hours
print 'By cost -',team_members_cost
print '---------------'

total_cost = 0
total_hours = 0

team_members_rates = team_members_rates_lumiere
team_members_hours = { 'lumiere.akapelonis': 0, 'lumiere.ppribyl': 0, 'lumiere.vli': 0, 'ext.gbolyuba': 0 }
team_members_cost = { 'lumiere.akapelonis': 0, 'lumiere.ppribyl': 0, 'lumiere.vli': 0, 'ext.gbolyuba': 0 }

for i in filter_array:
	worklogs = jira.worklogs(i.key)
	for team_member, rate in team_members_rates.items():
		for worklog in worklogs:
			if ((worklog.author.name == team_member) and (parser.parse(worklog.started)) <= parser.parse(end_date) and (parser.parse(worklog.started)) >= parser.parse(start_date)):
				team_members_hours[worklog.author.name] += (float(worklog.timeSpentSeconds)/3600)
				team_members_cost[worklog.author.name] += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_cost += ((float(worklog.timeSpentSeconds)/3600) * rate)
				total_hours += (float(worklog.timeSpentSeconds)/3600)

print 'Limiere - Total cost - ',total_cost,'CZK'
print 'Lumiere - Total hours -',total_hours,'hours'
print 'By hours -',team_members_hours
print 'By cost -',team_members_cost
