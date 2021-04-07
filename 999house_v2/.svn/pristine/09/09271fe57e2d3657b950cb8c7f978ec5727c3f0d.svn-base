#!/bin/sh
PATH=/usr/local/php/bin:/opt/someApp/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin # 将php路径加入都临时变量中

cd /mnt/e/project/bobing/ # 进入项目的根目录下，保证可以运行php think的命令
php think crontabmanage >> /mnt/e/project/testcrontab.txt # 执行在Test.php设定的名称



#安装：apt-get install cron
#启动：service cron start
#重启：service cron restart
#停止：service cron stop
#检查状态：service cron status
#查询cron可用的命令：service cron
#检查Cronta工具是否安装：crontab -l
#* * * * *　              每分钟执行
#*/1 * * * *　          每分钟执行
#0 5 * * *                每天五点执行
#0-59/2 * * * *        每隔两分钟执行，且是偶数分钟执行，比如2,4,6
#1-58/2 * * * *        每隔两分钟执行，且是奇数分钟执行，比如3,5,7
#0 0 1,5,10 * *        每个月1号，5号，10号执行
#0 0 1-5 * *            每个月 1到5号执行
