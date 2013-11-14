cd /home/rodger/web/mura-show.com
echo "copy uploads"
rsync -vzru  uploads/images rodger@home.mura-show.com:/var/www/rodger/www.mura-show.com/uploads --progress --partial
echo "copy thumbnails"
rsync -vzru  web/gallery rodger@home.mura-show.com:/var/www/rodger/www.mura-show.com/web --progress --partial
echo "prepare dump"
mysqldump -uroot -pparty3an --add-drop-table murashow > local.dump.sql
scp local.dump.sql rodger@home.mura-show.com:/var/www/rodger/www.mura-show.com/
echo "copy dump there and load"
ssh rodger@home.mura-show.com 'cd /var/www/rodger/www.mura-show.com && mysql -umura_show -pmuZUFwDx2B2tQfa8 www_mura_show_com < local.dump.sql && rm -f local.dump.sql';
echo "drop dump here"
rm -f local.dump.sql
