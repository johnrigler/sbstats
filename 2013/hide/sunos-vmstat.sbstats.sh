#!/bin/ksh

TARGET="/users/its/f848194/sbstats"
HOST=$1


ssh $HOST "

date '+Y:%Y.m:%m.d:%d.W:%W.w:%w.H:%H.M:%M.S:%S.Z:%Z'

/usr/sbin/psrinfo -v | grep -v Status | grep -v since | xargs -n14

vmstat 300 2 | tail -1

" > /tmp/$$.temp

cat /tmp/$$.temp


NOPRCS=`cat /tmp/$$.temp | grep operates | wc -l`
PROC=`cat /tmp/$$.temp | grep operates | head -1 | sed 's/,//g' | awk '{ print $2,$6,$7 }' | sed 's/ /-/g'`

echo "-------------------------------------------------"

eval `head -1 /tmp/$$.temp | sed 's/\./ /g'| sed 's/:/=/g'`

INFO2=`cat /tmp/$$.temp | tail -1 | while read A B C D E F G H I J K L M N O P Q
        do
        echo "$A $B $C $D $E $F $G $H $I $J $K $L $M $N $O $P $Q"
        done | sed 's/ /,/g'`


echo "-- $Y $m $d $W $w $H $M $S $Z -- $SERIALNO -- $NOPRCS-$PROC  ------------"
echo "-- $H:$M -- $INFO1 ---- $INFO2 --- "
echo "-----------------------------------"

INFO1="RESERVED"
SERIALNO="SER"
NOPRS=`echo $NOPRCS | sed 's/ //g'`
PROCINFO="$NOPRS"-"$PROC"

rm /tmp/$$.temp

MONTHTARGET=`echo "$TARGET/$Y/month/$m"`
WEEKTARGET=`echo "$TARGET/$Y/week/$W"`

mkdir -p $MONTHTARGET/byhost/$HOST
mkdir -p $WEEKTARGET/byhost/$HOST
mkdir -p $MONTHTARGET/byserial/$SERIALNO
mkdir -p $WEEKTARGET/byserial/$SERIALNO

 
FILE=`echo "$Y$m$d$W$w$Z":"$SERIALNO":"$PROCINFO":"$HOST":sunos:vmstat.sbstats"`

echo $FILE

echo "$H:$M $INFO1 $INFO2" >> $MONTHTARGET/byhost/$HOST/$FILE

ln -fs  ../../../../$Y/month/$m/byhost/$HOST/$FILE \
	$WEEKTARGET/byhost/$HOST/$FILE 

#ln -fs 	../../../../$Y/month/$m/byhost/$HOST/$FILE \
	# $MONTHTARGET/byserial/$SERIALNO/$FILE 

#ln -fs  ../../../../../$Y/month/$m/byhost/$HOST/$FILE \
	# $WEEKTARGET/byserial/$SERIALNO/$FILE



