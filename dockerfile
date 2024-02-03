FROM ubuntu:latest
ENV TZ=America/Chicago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
WORKDIR /mystuff
RUN apt-get update
# install apache web server
RUN apt-get install -y apache2
RUN apt-get install -y apache2-utils
# install php
RUN apt-get install -y php
RUN apt-get install -y php-bcmath
RUN apt-get install -y php-bz2
RUN apt-get install -y php-intl
RUN apt-get install -y php-gd
RUN apt-get install -y php-mbstring
RUN apt-get install -y php-mysql
RUN apt-get install -y php-zip
# install network commands (ip and ifconfig)
RUN apt-get install -y iproute2
RUN apt-get install -y net-tools
# install file converter from Windows to Linux
RUN apt-get install -y dos2unix
RUN apt-get clean
# copy in html and php files
COPY *.html   /mystuff/
COPY *.php   /mystuff/
#COPY myhack.txt   /mystuff/
COPY *.html /var/www/html/ 
COPY *.php /var/www/html/ 
EXPOSE 80
CMD ["apache2ctl","-D","FOREGROUND"]

