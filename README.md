# tagcommander_varcollector
Tool to get the TagCommander datalayer and export it into an excel File

_ QA_vars folder is the PHP part, that will transform all the data reveived into an Excel file
_ recupvars.js is the javascript you have to add to your browser in order to add and send the data on each page 
_ datalayer1433517216.xlsx is an example of the data you can get onces it is generated

Add the QA_Vars folder into your root folder
Be sure the Apache configuration accept all domains origins
The PHP script will write a new excel in the tmp folder file everytime it is called and delete all old files that are older than one hour

