rm /var/www/html/sst/hdd1.txt
rm /var/www/html/sst/hdd2.txt
rm /var/www/html/sst/hddteim.txt

echo Sript wird Gestartert

sudo /usr/sbin/hddtemp /dev/sd* >> /var/www/html/sst/hdd1.txt
sudo /usr/sbin/hddtemp /dev/sg* >> /var/www/html/sst/hdd2.txt
d=`date +%d.%m.%Y_%H:%M`
echo $d >> /var/www/html/sst/hddteim.txt

echo erledigt dateihen gespeichert
