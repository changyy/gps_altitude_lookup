# INPUT FILE #
```
> vim record/1.log
lat,lon
lat,lon
...
..
.
```

# OUTPUT #
```
> cat config.php
<?php
$google_api_key = "xxxxxx";

> php google_lookup.php
```

# GPX #
```
> vim record/1.log
:%s/\([0-9\.]*\),\([0-9\.]*\)/<trkpt lat="\1" lon="\2"><\/trkpt>/g
```

# AWK #
```
> grep eva google_query/1.log | awk -F ' ' '{print $3}' | awk -F ',' '{print $1}'
```

# Query merge #
```
> echo -e "{\n  \"results\": [ " > /tmp/result.log && cat google_query/1.log | sed 's/.*"status".*//' | sed 's/.*"results".*//' |  sed 's/.*],//' |  sed 's/^[}{]//' | sed '/^$/d' >> /tmp/result.log && echo -e "   ]\n}" >> /tmp/result.log
> vim /tmp/result.log
:%s/}\n/},\r/g
> cat /tmp/result.log | jq ''
```
# accuracy via sed #
```
> cat  /tmp/result.log | sed 's/\([0-9]*\.[0-9]\{9\}\)[0-9]*/\1/g' 
```
