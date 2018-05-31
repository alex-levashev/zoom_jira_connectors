#!/bin/bash
cd /var/www/html/charts/scripts/build_time/ && gnuplot build_time_gnu.txt
cd /var/www/html/charts/scripts/mt_stats/ && gnuplot mt_stats_gnu.txt
cd /var/www/html/charts/scripts/mt_stats_daily/ && gnuplot mt_stats_daily_gnu.txt
cd /var/www/html/charts/scripts/mt_utilization/ && gnuplot mt_utilization_gnu.txt
cd /var/www/html/charts/scripts/team_baz/ && gnuplot team_baz_gnu.txt
cd /var/www/html/charts/scripts/team_enc/ && gnuplot team_enc_gnu.txt
cd /var/www/html/charts/scripts/team_lumiere/&& gnuplot team_lumiere_gnu.txt
cd /var/www/html/charts/scripts/team_overall/ && gnuplot team_overall_gnu.txt
cd /var/www/html/charts/scripts/team_sc/ && gnuplot team_sc_gnu.txt
cd /var/www/html/charts/scripts/team_ua/ && gnuplot team_ua_gnu.txt
cd /var/www/html/charts/scripts/mt_stats/ && gnuplot mt_stats_gnu_inout.txt
