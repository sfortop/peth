FROM fortop/peth:core

ARG ENV=dev
COPY . /var/www/peth

WORKDIR /var/www/peth

COPY ./bin/do.sh /bin/do.sh

#RUN ls -la /bin/do.sh

RUN composer install -n --no-progress
CMD ["bash","-c","/bin/do.sh"]
#CMD ["php", "sleep.php"]

