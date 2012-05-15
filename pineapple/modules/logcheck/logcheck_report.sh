#!/bin/sh

LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usb/lib:/usb/usr/lib
PATH=$PATH:/usb/usr/bin:/usb/usr/sbin

MYPATH=/www/pineapple/modules/logcheck/
BIN=ssmtp
_FILE=${MYPATH}events.tmp

grep -hv -e ^# ${MYPATH}rules/match -e ^$ > ${MYPATH}rules/match.tmp
grep -hv -e ^# ${MYPATH}rules/ignore -e ^$ > ${MYPATH}rules/ignore.tmp

cat ${MYPATH}events | grep -Ef ${MYPATH}rules/match.tmp | grep -vEf ${MYPATH}rules/ignore.tmp > ${MYPATH}events.tmp

if [ -s ${_FILE} ]
then

TO=`cat ${MYPATH}logcheck.conf | awk 'NR==1'`
FROM=`cat ${MYPATH}logcheck.conf | awk 'NR==2'`
SUBJECT=`cat ${MYPATH}logcheck.conf | awk 'NR==3'`

echo -e "To: ${TO}" > ${MYPATH}mail.tmp
echo -e "From: ${FROM}" >> ${MYPATH}mail.tmp
echo -e "Subject: ${SUBJECT}" >> ${MYPATH}mail.tmp
echo -e "\n" >> ${MYPATH}mail.tmp
echo -e "[Logcheck]" >> ${MYPATH}mail.tmp

cat ${MYPATH}events.tmp >> ${MYPATH}mail.tmp

${BIN} -t < ${MYPATH}mail.tmp

rm -rf ${MYPATH}events
rm -rf ${MYPATH}mail.tmp

killall logread && echo ${MYPATH}logcheck.sh | at now

fi

rm -rf ${MYPATH}events.tmp