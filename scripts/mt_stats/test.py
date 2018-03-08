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

start_dates = ["2017-01-02", "2017-01-09", "2017-01-16", "2017-01-23", "2017-01-30","2017-02-06", "2017-02-13","2017-02-20","2017-02-27","2017-03-06","2017-03-13","2017-03-20","2017-03-27","2017-04-03","2017-04-10","2017-04-17","2017-04-24","2017-05-01","2017-05-08","2017-05-15","2017-05-22","2017-05-29","2017-06-05","2017-06-12","2017-06-19","2017-06-26","2017-07-03","2017-07-10","2017-07-17","2017-07-24","2017-07-31","2017-08-07","2017-08-14","2017-08-21","2017-08-28","2017-09-04","2017-09-11"]

for start_date in start_dates:
    end_date = (parser.parse(start_date) - datetime.timedelta(days=6)).strftime("%Y-%m-%d")
    filter02 = 'status changed to Closed during (-7d, '+start_date+') AND status = Closed AND "Discovered During" = customer'
    filter01 = '"Discovered During" = customer AND createdDate >= '+end_date+' AND createdDate < '+start_date+''
    # filter02 = '"Discovered During" = customer AND status changed to Closed during ('+end_date+', '+start_date+') AND status = Closed'
    # print end_date+','str(jira.search_issues(filter01, maxResults=1000).total)+','+str(jira.search_issues(filter02, maxResults=1000).total)
    # print filter01
    print start_date+','+str(jira.search_issues(filter01, maxResults=1000).total)+','+str(jira.search_issues(filter02, maxResults=1000).total)

# if os.path.isfile('../../data/mt_stats_inout.txt') == True:
#     with open('../../data/mt_stats_inout.txt', 'a') as file:
#         file.write(today_date+','+str(jira.search_issues(filter05, maxResults=1000).total)+','+str(jira.search_issues(filter06, maxResults=1000).total)+'\n')
# else:
#     with open('../../data/mt_stats_inout.txt', 'w') as file:
#         file.write('Date,"Issue in - Last week","Issues out - Last week"\n')
#         file.write(today_date+','+str(jira.search_issues(filter05, maxResults=1000).total)+','+str(jira.search_issues(filter06, maxResults=1000).total)+'\n')
#
# bashCommand = "gnuplot ../../scripts/mt_stats/mt_stats_gnu_inout.txt"
# process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
# output, error = process.communicate()
