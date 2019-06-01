# neblidex_seeder
Code that obtains the DNS seed list and displays it

Must be ran via PHP. 

getdnsseed.php is typically ran every 15 minutes via Cron Job.
NebliDex itself utilizes showdnsseed for obtaining DNS seed list and initial IP discovery

It is important to reformat getdnsseed to an index file if using it as a default DNS seed.
If file placement at domain is this: mydomain.com/seedfile/getdnsseed.php, then change to mydomain.com/seedfile/index.php and in NebliDex, enter seed as mydomain.com/seedfile
