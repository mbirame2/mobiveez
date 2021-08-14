sudo git add .
sudo git commit -m 'iveez deploy'
sudo git push 
sudo ssh mbirame@192.99.69.14 -p 57829
cd /var/www/mobiveez/
sudo git pull