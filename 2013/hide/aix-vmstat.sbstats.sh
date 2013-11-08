#!/bin/ksh

TARGET="/users/its/f848194/sbstats"
HOST=$1


ssh $HOST "

date '+Y:%Y.m:%m.d:%d.W:%W.w:%w.H:%H.M:%M.S:%S.Z:%Z'
lsattr -El sys0 | grep systemid | sed 's/,/ /' | cut -c 21-29
lsattr -El proc0 | cut -c 13-27 | xargs -n100 | sed 's/ /-/g'
vmstat 300 1

" > /tmp/$$.temp

echo "-------------------------------------------------"

cat /tmp/$$.temp

echo "-------------------------------------------------"

eval `head -1 /tmp/$$.temp | sed 's/\./ /g'| sed 's/:/=/g'`
SERIALNO=`head -2 /tmp/$$.temp | tail -1`
PROCINFO=`head -3 /tmp/$$.temp | tail -1`

INFO1=`grep System /tmp/$$.temp | awk '{ print $3,$4 }' | sed 's/=/ /g' | sed 's/MB//g' | awk '{print $2,$4 }' \
        | sed 's/ /,/'`

INFO2=`cat /tmp/$$.temp | tail -1 | while read A B C D E F G H I J K L M N O P Q
        do
        echo "$A $B $C $D $E $F $G $H $I $J $K $L $M $N $O $P $Q"
        done | sed 's/ /,/g'`


echo "-- $Y $m $d $W $w $H $M $S $Z -- $SERIALNO -- $PROCINFO ------------"
echo "-- $H:$M -- $INFO1 ---- $INFO2 --- "
echo "-----------------------------------"

rm /tmp/$$.temp

MONTHTARGET=`echo "$TARGET/$Y/month/$m"`
WEEKTARGET=`echo "$TARGET/$Y/week/$W"`

mkdir -p $MONTHTARGET/byhost/$HOST
mkdir -p $WEEKTARGET/byhost/$HOST
mkdir -p $MONTHTARGET/byserial/$SERIALNO
mkdir -p $WEEKTARGET/byserial/$SERIALNO


FILE=`echo "$Y$m$d$W$w$Z":"$SERIALNO":"$PROCINFO":"$HOST":aix:vmstat.sbstats"`

echo $FILE

echo "$H:$M $INFO1 $INFO2" >> $MONTHTARGET/byhost/$HOST/$FILE

ln -fs  ../../../../$Y/month/$m/byhost/$HOST/$FILE \
	$WEEKTARGET/byhost/$HOST/$FILE 

#ln -fs 	../../../../$Y/month/$m/byhost/$HOST/$FILE \
	# $MONTHTARGET/byserial/$SERIALNO/$FILE 

#ln -fs  ../../../../../$Y/month/$m/byhost/$HOST/$FILE \
	# $WEEKTARGET/byserial/$SERIALNO/$FILE



