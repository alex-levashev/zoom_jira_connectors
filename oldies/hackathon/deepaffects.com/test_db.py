#!/usr/bin/python
import MySQLdb

db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="hack",         # your username
                     passwd="hack",  # your password
                     db="hack")        # name of the data base


# you must create a Cursor object. It will let
#  you execute all the queries you need
cur = db.cursor()

# Check if table exists

#cur.execute("SHOW TABLES LIKE 'Persons1'")
#result = cur.fetchone()
#if result:
#    print ('there is a table named "tableName"')
#else:
#    print ('there are no tables named "tableName"')

# Create table
#cur.execute("CREATE TABLE Persons (PersonID int, LastName varchar(255), FirstName varchar(255), Address varchar(255), City varchar(255))")

	
# Insert data into table
#cur.execute("INSERT INTO Persons (PersonID, LastName, FirstName, Address, City) VALUES ('0', 'levashev0', 'Alexey', 'NoWhere', 'NoCity')")
#cur.execute("INSERT INTO Persons (PersonID, LastName, FirstName, Address, City) VALUES ('1', 'levashev1', 'Alexey', 'NoWhere', 'NoCity')")
#cur.execute("INSERT INTO Persons (PersonID, LastName, FirstName, Address, City) VALUES ('2', 'levashev2', 'Alexey', 'NoWhere', 'NoCity')")
#cur.execute("INSERT INTO Persons (PersonID, LastName, FirstName, Address, City) VALUES ('3', 'levashev3', 'Alexey', 'NoWhere', 'NoCity')")
#cur.execute("INSERT INTO Persons (PersonID, LastName, FirstName, Address, City) VALUES ('4', 'levashev4', 'Alexey', 'NoWhere', 'NoCity')")

cur.execute("SELECT * FROM Persons;")
print "Before inserting : " + str(cur.rowcount)

id = cur.rowcount + 1
lastname = raw_input('Enter last name : ')
firstname = raw_input('Enter first name : ')
address = raw_input('Enter address : ')
city = raw_input('Enter city : ')

query = 'INSERT INTO Persons (PersonID, LastName, FirstName, Address, City) VALUES ("' + str(id) + '", "' + str(lastname) + '", "' + str(firstname) + '", "' + str(address) + '", "' + str(city) + '")'
cur.execute(query)

cur.execute("SELECT * FROM Persons;")
print "After inserting : " + str(cur.rowcount)

db.commit()
db.close()
