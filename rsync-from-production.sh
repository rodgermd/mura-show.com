echo "copy uploads"
rsync -vzru  rodger@www.mura-show.com:/var/www/rodger/www.mura-show.com/uploads/images uploads/ --progress --partial
echo "copy thumbnails"
rsync -vzru  rodger@www.mura-show.com:/var/www/rodger/www.mura-show.com/web/gallery web/ --progress --partial
echo "prepare dump"
ssh rodger@www.mura-show.com 'cd /var/www/rodger/www.mura-show.com && mysqldump -umura_show -pmuZUFwDx2B2tQfa8 www_mura_show_com --add-drop-table > dump.sql';
echo "copy dump here"
scp rodger@www.mura-show.com:/var/www/rodger/www.mura-show.com/dump.sql .
echo "drop dump there"
ssh rodger@www.mura-show.com 'cd /var/www/rodger/www.mura-show.com && rm -f dump.sql'
echo "load dump"
mysql -uroot -pparty3an rodger__mura_show < dump.sql
