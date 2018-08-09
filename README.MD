# PHP Transparent Proxy Script
Proxy (Noun): A Process That Accepts Requests Fpr Some Service And Passes Them On To The Real Server

# What This Is ***NOT***
PHP Transparent Proxy Is Not A Server Proxy That Redirects Requests. In Other Words, It's Not A Server That You Would Add To Your Browser And That Would "Pretend" To Be From Somewhere Else. It Can't Be Used To Bypass A Firewall At Work Or At School

# Extended Support
If You Need Extended Support Than What This `README` Gives, Please Open An [Issue](https://github.com/KrozerYT/PHP-Transparent-Proxy/issues) And I Will Help!

# What This Proxy Supports:
- GET And POST Requests (POST Was The Whole Reason For This, Since JsonP Doesn't Support It!)
- HTTP REFERER Check (Only Accept Requests From One Server)
- COOKIES, In Both Directions. Technically Part Of HEADERS, But It's Worth Mentioning!
- HEADERS In Both Directions (With Specific Exceptions, See Limitations)

# What It Doesn't Support (Yet, Maybe):
- Dynamic Destionation (Though That's Relatively Trival To Change), Because I Don't Need It
- Load Balancing/Cycling, I May Add This As A Personal Exercise In The Future
- Authentication, Beyond The Referrer Check, Or Session (This Should Be Handled By The Backend Anyway)

# Quick Steps
 - Download `Proxy.PHP`
 
 - Modify `Proxy.PHP` And Change The `$DestinationURL` To The URL Where Your Backend Server-Side Script Is (Meaning, Where You Want To Pull The Data From)

- Place The `Proxy.PHP` Script Somewhere On Your Website Hosting, On The Same Server You Want To Use It From

- Change Your Front-End JavaScript To Use The `Proxy.PHP` Instead Of The Backend URL

# Limitations

## Headers
While This Proxy Attemts To Be as Transparent As Possible, There Are A Couple Of Things That Break This (Unless I Can Find a Way To Fix Them In The Future)

**Headers From The Backend To The Client**
Any And All Headers Should Be Sent From The Backend To The Client With No Modification. Note However That PHP Simply Overwrites Existing Headers With The `Header` Function. If The PHP Proxy Or The Server On Which It Resides Adds Headers That Are Not Existent On The Backend, They Will Remain

So, You Have The Headers From The Backend Untouched Along With Any Extra Headers On The Proxy That Aren't Overwritten

**Headers From The Client To The Backend**
The Following Headers Are Forced By The Proxy:

- `Host`: Is Forced Because, Obviously, It's The Address Of The Backend We Need To Set
- `GET` Or `POST`: Is Forced Because We Are Addressing A Different Path As Well As Adding The Query String For `GET` Requests
- `Accept-Charset`: Is Forced To `ISO-8859-1...` Because It Doesn't Work Without It, Beats Me Why (I Get A 400 Error)
- `Connection`: Is Forced To `Close` At The End Of The Request (Before The Data) Because Keep-Alive Isn't Supported.

The Last Bit Means That This Proxy Is Non-Streaming. If It Were, My Browser Sending `Connection: Keep-Alive` Would Work Fine And I'd Jump For Joy... But That's Not The Case

# Note
In Order For This To Work Correctly, Rename `Proxy.PHP` To All Lowercase

# Contributors
 - Krozer
 
# License
BSD 3-Clause "New" Or "Revised" License
