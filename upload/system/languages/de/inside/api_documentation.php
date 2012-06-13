<?php

$lang = array (
  'api_doc_page_title' => 'API Documentation - #SITE_TITLE#',
  'api_doc_menu_title' => 'API Documentation',
  'api_doc_cat_num' => '16',
  'api_doc_cat_1' => 'Getting Started',
  'api_doc_cat_2' => 'OAuth FAQ',
  'api_doc_cat_3' => 'Statuses Methods',
  'api_doc_cat_4' => 'Users Methods',
  'api_doc_cat_5' => 'Direct Messages',
  'api_doc_cat_6' => 'Friendships Methods',
  'api_doc_cat_7' => 'Social Methods',
  'api_doc_cat_8' => 'Account Methods',
  'api_doc_cat_9' => 'Groups Methods',
  'api_doc_cat_10' => 'Favorites Methods',
  'api_doc_cat_11' => 'Saved Searches',
  'api_doc_cat_12' => 'OAuth Methods',
  'api_doc_cat_13' => 'Using User\'s Data',
  'api_doc_cat_14' => 'Test',
  'api_doc_cat_15' => 'Search',
  'api_doc_cat_16' => 'Trends',
  'api_doc_cant_understand' => 'Still don\'t understand something? You can allways ask our support.',
  'api_doc_contact_us' => 'Contact Us',
  'api_doc_cat_1_post_num' => '8',
  'api_doc_cat_1_post_1_title' => '#SITE_TITLE# API Overview',
  'api_doc_cat_1_post_1_text' => 'The [b]#SITE_TITLE# API[/b] is inspired by [b]Twitter\'s API[/b] and gives you a simple and structured way to access the powerful capabilities of the [b]#SITE_TITLE#[/b]. The API lets you programmatically search, retrieve, create and destroy authenticated member profile, group and other data from our site. Our API capabilities let you dynamically add new content as well as status updates, comment on existing statuses, manage user\'s profile, groups, direct messages, favorites, trends and live search of the [b]#SITE_TITLE#[/b] database. The API is based on open standards like XML, RSS, JSON and Atom.',
  'api_doc_cat_1_post_2_title' => 'Authentication',
  'api_doc_cat_1_post_2_text' => 'Not all functionalities offered by our API require authentication. Functionalities with free access are those that everyone can access freely and this do not include modification options like deleting, editing, adding of data and reading of personal data of our users.

The API has a few methods that would require the user to authenticate in order to access and/or publish data. For instance, functionality on [b]#SITE_TITLE#[/b] available to logged in users, such as submitting updates or downloading direct messages, are similarly protected through the API. In order to protect the data of our clients we use the OAuth protocol for authenticating the application\'s access to the data of our users.',
  'api_doc_cat_1_post_3_title' => 'OAuth',
  'api_doc_cat_1_post_3_text' => '[url=http://apiwiki.twitter.com/Authentication]http://apiwiki.twitter.com/Authentication[/url]

OAuth is a token-passing mechanism that allows users to control which applications have access to their data without giving away their passwords. More information on the OAuth specification can be found at [url=http://www.oauth.net]oauth.net[/url] or in the excellent Beginner\'s Guide to OAuth from [url=http://hueniverse.com/oauth/]Hueniverse[/url].

Registration for OAuth applications for [b]#SITE_TITLE#[/b] is available at [url=#SITE_URL#api]#SITE_URL#api[/url].

Access tokens should never expire. An user\'s access token will be invalidated if the user explicitly revokes your application\'s authorization or if [b]#SITE_TITLE#[/b] suspends your application. If your application is suspended there will be a note on your application page saying that it has been suspended. In either case, your application should be able to handle token revokation gracefully.

Many users trust an application to read their information but not necessarily change their name or post new statuses. OAuth permissions are granular, allowing applications and users to agree on Read-Only or Read-Write access. Updating information through the API, be it name, location or adding updating status, requires an HTTP POST. Any API method that requires an HTTP POST is considered a write method and requires Read-Write access.',
  'api_doc_cat_1_post_4_title' => 'Desktop Clients',
  'api_doc_cat_1_post_4_text' => '[url=http://apiwiki.twitter.com/Authentication]http://apiwiki.twitter.com/Authentication[/url]

The traditional OAuth flow for desktop clients can be cumbersome. We\'ve created a verifier-based experience for destkop clients that use the following flow:

1.The application uses [b]oauth/request_token[/b] to obtain a request token from [b]#SITE_TITLE#[/b].
2.The application directs the user to [b]oauth/authorize[/b] on [b]#SITE_TITLE#[/b].
3.After obtaining approval from the user, a prompt on [b]#SITE_TITLE#[/b] will display a 7 digit verifier.
4.The user is instructed to copy this verifier and return to the appliction.
5.The application will prompt the user to enter the verifier from step 4.
6.The application uses the verifier as the value for the [b]oauth_verifier[/b] parameter in a call to [b]oauth/access_token[/b] which will verify the verifier and exchange a [b]request_token[/b] for an [b]access_token[/b].
7.Twitter will return an [b]access_token[/b] for the application to generate subsequent OAuth signatures.',
  'api_doc_cat_1_post_5_title' => 'Rate limiting',
  'api_doc_cat_1_post_5_text' => '[url=http://apiwiki.twitter.com/Rate-limiting]http://apiwiki.twitter.com/Rate-limiting[/url]

The default rate limit for calls to the API is 150 requests per hour. The API does account- and IP-based rate limiting. Authenticated API calls are charged to the authenticating user\'s limit while unauthenticated API calls are deducted from the calling IP address\' allotment.

Rate limiting only applies to methods that request information with the HTTP GET command. API methods that use HTTP POST to submit data to [b]#SITE_TITLE#[/b], such as [b]statuses/update[/b] do not affect rate limits. Additionally, requests to [b]account/rate_limit_status[/b], and any of the OAuth endpoints are not charged to a limit. These unlimited methods are still subject to daily update and follower limits to promote healthy use and discourage spam.

Your application should recognize it is being rate-limited by the API if it receives begins to receive HTTP 400 response codes. It is best practice for applications to monitor their current rate limit status and dynamically throttle requests if necessary. The API offers a way to observe this status:

The [b]account/rate_limit_status[/b] method. Calling this method does not count against the requestor\'s API limit.The response including:

[b]&lt hash&gt
&lt remaining&gt 150 &lt/remaining&gt
&lt hourly-limit>150 &lt/hourly-limit&gt
&lt reset-time&gt 7 &lt/reset-time&gt
&lt /hash&gt[/b]',
  'api_doc_cat_1_post_6_title' => 'HTTP Status Codes and Errors',
  'api_doc_cat_1_post_6_text' => '[url=http://apiwiki.twitter.com/HTTP-Response-Codes-and-Errors]http://apiwiki.twitter.com/HTTP-Response-Codes-and-Errors[/url]

The API attempts to return appropriate HTTP status codes for every request. It is possible to surpress response codes for the API.

[b]200 OK[/b]: Success!
[b]304 Not Modified[/b]: There was no new data to return.
[b]400 Bad Request[/b]: The request was invalid.  An accompanying error message will explain why. This is the status code will be returned during rate limiting.
[b]401 Unauthorized[/b]: Authentication credentials were missing or incorrect.
[b]403 Forbidden[/b]: The request is understood, but it has been refused.  An accompanying error message will explain why. This code is used when requests are being denied due to update limits.
[b]404 Not Found[/b]: The URI requested is invalid or the resource requested, such as a user, does not exists.
[b]500 Internal Server Error[/b]: Something is broken.  Please post to the group so the Opentronix team can investigate.',
  'api_doc_cat_1_post_7_title' => 'Callback and Suppress Response Codes',
  'api_doc_cat_1_post_7_text' => '[url=http://apiwiki.twitter.com/Things-Every-Developer-Should-Know]http://apiwiki.twitter.com/Things-Every-Developer-Should-Know[/url]

There are two special parameters in the [b]#SITE_TITLE#[/b] API:

[b]callback[/b]: Used only when requesting JSON formatted responses, this parameter wraps your response in a callback method of your choice.  For example, appending [b]&callback=myFancyFunction[/b] to your request will result in a response body of: myFancyFunction(...).  Callbacks may only contain alphanumeric characters and underscores; any invalid characters will be stripped.

[b]suppress_response_codes[/b]: If this parameter is present, all responses will be returned with a 200 OK status code - even errors.  This parameter exists to accommodate Flash and JavaScript applications running in browsers that intercept all non-200 responses.  If used, it\'s then the job of the client to determine error states by parsing the response body.  Use with caution, as those error messages may change.

Where noted, some API methods will return different results based on HTTP headers sent by the client. Where the same behavior can be controlled by both a parameter and an HTTP header, the parameter will take precedence.',
  'api_doc_cat_1_post_8_title' => 'Error Messages',
  'api_doc_cat_1_post_8_text' => 'When the [b]#SITE_TITLE#[/b] API returns error messages, it does so in your requested format. For example, an error from an XML method might look like this:

[b]&lt&#63xml version="1.0" encoding="UTF-8" &#63&gt
&lt hash&gt
&lt request&gt /direct_messages/destroy/456.xml &lt/request&gt
&lt error&gt No direct message with that ID found. &lt/error&gt
&lt/hash&gt[/b]',
  'api_doc_cat_2_post_num' => '8',
  'api_doc_cat_2_post_1_title' => 'What is OAuth?',
  'api_doc_cat_2_post_1_text' => '[url=http://apiwiki.twitter.com/OAuth-FAQ#WhatisOAuth]http://apiwiki.twitter.com/OAuth-FAQ#WhatisOAuth[/url]

OAuth is an authentication protocol that allows users to approve application to act on their behalf without sharing their password. More information can be found at [url=http://www.oauth.net]oauth.net[/url] or in the excellent Beginner\'s Guide to OAuth from [url=http://hueniverse.com/oauth/]Hueniverse[/url].',
  'api_doc_cat_2_post_2_title' => 'Where do I create an application?',
  'api_doc_cat_2_post_2_text' => '#SITE_URL#api',
  'api_doc_cat_2_post_3_title' => 'How long does an access token last?',
  'api_doc_cat_2_post_3_text' => '[url=http://apiwiki.twitter.com/OAuth-FAQ#Howlongdoesanaccesstokenlast]http://apiwiki.twitter.com/OAuth-FAQ#Howlongdoesanaccesstokenlast[/url]

We do not currently expire access tokens. You access token will be invalid if a user explicitly rejects your application from their settings or if a [b]#SITE_TITLE#[/b] admin suspends your application. If your application is suspended there will be a note on your application page saying that it has been suspended.',
  'api_doc_cat_2_post_4_title' => 'The application registration page asks about read/write access. What constitutes a write?',
  'api_doc_cat_2_post_4_text' => 'http://apiwiki.twitter.com/OAuth-FAQ#Theapplicationregistrationpageasksaboutread/writeaccessWhatconstitutesawrite

Many users trust an application to read their information but not necessarily change their name or post new statuses. Updating information via the [b]#SITE_TITLE#[/b] API - be it name, location or adding a new status - requires and HTTP POST. We stuck with the same restriction when implementing this. Any API method that requires an HTTP POST is considered a write method and requires read & write access.',
  'api_doc_cat_2_post_5_title' => 'What encryption algorithms do you use?',
  'api_doc_cat_2_post_5_text' => 'To encrypt requests to a given resource you can use only the [b]HMAC-SHA1[/b] algorithm. We do not support HTTPS protocol for data transfer in [b]#SITE_TITLE#[/b], which means we do not support plaintext signatures. All requests must be encrypted by signatures created with [b]HMAC-SHA1[/b] algorithm.',
  'api_doc_cat_2_post_6_title' => 'What is the process of creating a page signature?',
  'api_doc_cat_2_post_6_text' => 'To create a page signature, please follows the original algorithm presented in [url=http://www.oauth.net]oauth.net[/url], you can read details here [url=http://oauth.net/core/1.0/#signing_process]http://oauth.net/core/1.0/#signing_process[/url].

The main things to look out for are:

1.Each parameter has to be in [b]UTF8[/b] format and [b]urlencoded[/b].
2.Parameters have to be in [b]alphabetical order[/b].
3.[b]Oauth_signature[/b] parameter is [b]not included[/b] with the other parameters when the signature is created.
4.Timestamp is expressed in the number of seconds since January 1, 1970 00:00:00 GMT. The timestamp value [b]MUST[/b] be a positive integer and [b]MUST[/b] be equal or greater than the timestamp used in previous requests.
5.Nonce [b]MUST[/b] be unique for all requests with that timestamp. A nonce is a random string, uniquely generated for each request.',
  'api_doc_cat_2_post_7_title' => 'Is it necessary to use OAuth and create signature for every page? ',
  'api_doc_cat_2_post_7_text' => 'There are free access pages that anyone can use without authorizing to the API. These pages display only general information accessible to all users. For example, you can visit recent posts in your network without authorization.',
  'api_doc_cat_2_post_8_title' => 'How to use API with third party applications?',
  'api_doc_cat_2_post_8_text' => 'For url base use the following address: #SITE_URL#1',
  'api_doc_cat_3_post_num' => '15',
  'api_doc_cat_3_post_1_title' => 'statuses/public_timeline',
  'api_doc_cat_3_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-public_timeline]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-public_timeline[/url]

Latest 20 statuses  from non-protected users who have set a custom user icon.

[i]Usage[/i]: #SITE_URL#1/statuses/public_timeline.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, RSS, JSON and Atom
[i]Requires Authentication[/i]: false (If you are authorized, you will also get statuses from private groups, where you are a member)
[i]API rate limited[/i]: true
[i]Parameters[/i]: none
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-public_timeline]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-public_timeline[/url]

[b]Example[/b]: #SITE_URL#1/statuses/public_timeline.json',
  'api_doc_cat_3_post_2_title' => 'statuses/user_timeline',
  'api_doc_cat_3_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-user_timeline]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-user_timeline[/url]

Latest 20 statuses posted from the authenticating user. You can request another user\'s timeline via the id parameter as well.

[i]Usage[/i]: #SITE_URL#1/statuses/user_timeline.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, RSS, JSON and Atom
[i]Requires Authentication[/i]: false (If you are authorized, you will also get statuses from private groups, where you are a member)
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-user_timeline]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-user_timeline[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-user_timeline]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-user_timeline[/url]

[b]Example[/b]: #SITE_URL#1/statuses/user_timeline/1234.json?suppress_response_codes',
  'api_doc_cat_3_post_3_title' => 'statuses/mentions',
  'api_doc_cat_3_post_3_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions[/url]

Latest 20 mentions (posts that contain @username) for the authenticating user.

[i]Usage[/i]: #SITE_URL#1/statuses/mentions.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, RSS, JSON and Atom
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions[/url]

[b]Example[/b]: #SITE_URL#1/statuses/mentions.xml',
  'api_doc_cat_3_post_4_title' => 'statuses/private_mentions',
  'api_doc_cat_3_post_4_text' => '
Latest 20 mentions in private posts (posts that contain @username) for the authenticating user.

[i]Usage[/i]: #SITE_URL#1/statuses/private_mentions.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, RSS, JSON and Atom
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions[/url]

[b]Example[/b]: #SITE_URL#1/statuses/private_mentions.xml',
  'api_doc_cat_3_post_5_title' => 'statuses/update',
  'api_doc_cat_3_post_5_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0update] apiwiki.twitter.com/statuses update [/url]

[i]Usage[/i]: #SITE_URL#1/statuses/update.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0update]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions[/url]
[i]Additional Parameters to attach to your post[/i]: link(valid url), video|file|image(raw multipart data, not a URL to a file|image|video)
[i]Unsupported Parameters[/i]: lat, long, place_id, display_coordinates
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0update]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-mentions[/url]

[b]Example[/b]: #SITE_URL#1/statuses/update.xml?status=hello
[b]Example[/b]: #SITE_URL#1/statuses/update.xml?status=hello&in_reply_to_status_id=1234
[b]Example[/b]: #SITE_URL#1/statuses/update.xml?status=hello&link=your_link
[b]Example[/b]: #SITE_URL#1/statuses/update.xml?status=hello&video=
[b]Example[/b]: #SITE_URL#1/statuses/update.xml?status=hello&file=',
  'api_doc_cat_3_post_6_title' => 'statuses/group_update',
  'api_doc_cat_3_post_6_text' => '
Updates the authenticating user\'s status in specific group.

[i]Usage[/i]: #SITE_URL#1/statuses/group_update.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false

[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0update]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-update[/url] and specify the group id: #SITE_URL#1/statuses/group_update/group_id.format
or group name: #SITE_URL#1/statuses/group_update/group_name.format

[i]Unsupported Parameters[/i]: lat, long, place_id, display_coordinates
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0update]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-update[/url]

[b]Example[/b]: #SITE_URL#1/statuses/group_update.xml?status=hello
[b]Example[/b]: #SITE_URL#1/statuses/group_update.xml?status=hello&in_reply_to_status_id=1234',
  'api_doc_cat_3_post_7_title' => 'statuses/destroy',
  'api_doc_cat_3_post_7_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0destroy] apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-destroy [/url]

Destroys the post specified by the required ID parameter.  The authenticating user must be the author of the specified post.

[i]Usage[/i]: #SITE_URL#1/statuses/destroy/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: id of the status
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-destroy[/url]

[b]Example[/b]: #SITE_URL#1/statuses/destroy/1234.xml',
  'api_doc_cat_3_post_8_title' => 'statuses/private_destroy',
  'api_doc_cat_3_post_8_text' => '
Destroys the private post specified by the required ID parameter.  The authenticating user must be the author of the specified private post.

[i]Usage[/i]: #SITE_URL#1/statuses/private_destroy/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: id of the private status
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-destroy[/url]

[b]Example[/b]: #SITE_URL#1/statuses/private_destroy/1234.xml',
  'api_doc_cat_3_post_9_title' => 'statuses/friends',
  'api_doc_cat_3_post_9_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0friends] apiwiki.twitter.com/Twitter-REST-API-Method:-statuses friends[/url]

Returns a user\'s friends, each with current status inline. It\'s also possible to request another user\'s friends list via the id, screen_name or user_id parameter.

[i]Usage[/i]: #SITE_URL#1/statuses/friends.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0friends]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends[/url]
[i]Unsupported Parameters[/i]: cursor
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0friends]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends[/url]

[b]Example[/b]: #SITE_URL#1/statuses/friends.json?user_id=1234',
  'api_doc_cat_3_post_10_title' => 'statuses/followers',
  'api_doc_cat_3_post_10_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0followers] apiwiki.twitter.com/Twitter-REST-API-Method:-statuses followers[/url]

Returns a user\'s followers, each with current status inline. It\'s also possible to request another user\'s followers list via the id, screen_name or user_id parameter.

[i]Usage[/i]: #SITE_URL#1/statuses/followers.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0followers]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-followers[/url]
[i]Unsupported Parameters[/i]: cursor
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0followers]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-followers[/url]

[b]Example[/b]: #SITE_URL#1/statuses/followers.json?user_id=1234',
  'api_doc_cat_3_post_11_title' => 'statuses/friends_timeline',
  'api_doc_cat_3_post_11_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends_timeline] apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends_timeline[/url]

Latest 20 statuses posted by the authenticating user and that user\'s friends.

[i]Usage[/i]: #SITE_URL#1/statuses/friends_timeline.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON, Atom, RSS
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends_timeline]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends_timeline[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends_timeline]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-friends_timeline[/url]

[b]Example[/b]: #SITE_URL#1/statuses/friends_timeline.json',
  'api_doc_cat_3_post_12_title' => 'statuses/show',
  'api_doc_cat_3_post_12_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0show] apiwiki.twitter.com/Twitter-REST-API-Method:-statuses show[/url]

[i]Usage[/i]: #SITE_URL#1/statuses/show/id.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON, Atom, RSS
[i]Requires Authentication[/i]: false (If you are authorized, you could get a status from private groups, where you are a member)
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-show[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses%C2%A0show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-statuses-show[/url]

[b]Example[/b]: #SITE_URL#1/statuses/show/1234.json',
  'api_doc_cat_3_post_13_title' => 'statuses/commented',
  'api_doc_cat_3_post_13_text' => '
Returns all post by the user that have comments.

[i]Usage[/i]: #SITE_URL#1/statuses/commented.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: type: public or private statuses
[i]Response[/i]:
[b]&lt statuses&gt
&lt post&gt
&lt id&gt post_id &lt/id&gt
&lt/post&gt
&lt/statuses&gt[/b]

[b]Example[/b]: #SITE_URL#1/statuses/commented.json?type=public',
  'api_doc_cat_3_post_14_title' => 'statuses/comments',
  'api_doc_cat_3_post_14_text' => '
Returns all comments to a selected post.

[i]Usage[/i]: #SITE_URL#1/statuses/comments.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: post_id: #SITE_URL#1/statuses/comments.format?post_id=123
[i]Response[/i]:
[b]&lt comments&gt
&lt comment&gt
&lt id&gt comment_id &lt/id&gt
&lt text&gt comment_text &lt/text&gt
&lt/comment&gt
&lt/comments&gt[/b]

[b]Example[/b]: #SITE_URL#1/statuses/comments.xml?post_id=123',
  'api_doc_cat_3_post_15_title' => 'statuses/private_comments',
  'api_doc_cat_3_post_15_text' => '
Returns all comments to a selected private post.

[i]Usage[/i]: #SITE_URL#1/statuses/private_comments.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: post_id: #SITE_URL#1/statuses/private_comments.format?post_id=123
[b]&lt comments&gt
&lt comment&gt
&lt id&gt comment_id &lt/id&gt
&lt text&gt comment_text &lt/text&gt
&lt/comment&gt
&lt/comments&gt[/b]

[b]Example[/b]: #SITE_URL#1/statuses/private_comments.xml?post_id=123',
  'api_doc_cat_4_post_num' => '4',
  'api_doc_cat_4_post_1_title' => 'users/show',
  'api_doc_cat_4_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users%C2%A0show] apiwiki.twitter.com/Twitter-REST-API-Method:-users show[/url]

[i]Usage[/i]: #SITE_URL#1/users/show.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users%C2%A0show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-show[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users%C2%A0show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-show[/url]

[b]Example[/b]: #SITE_URL#1/users/show/1234.xml',
  'api_doc_cat_4_post_2_title' => 'users/lookup',
  'api_doc_cat_4_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-lookup]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-lookup[/url]

[i]Usage[/i]: #SITE_URL#1/users/lookup.format
[i]Method[/i]: GET, POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-lookup]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-lookup[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-lookup]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-lookup[/url]

[b]Example[/b]: #SITE_URL#1/users/lookup.json?user=1234,5678&screen_name=chuck,david
',
  'api_doc_cat_4_post_3_title' => 'users/search',
  'api_doc_cat_4_post_3_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-search]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-search[/url]

Returns all users that has been found by certain criteria

[i]Usage[/i]: #SITE_URL#1/users/search.format
[i]Method[/i]: GET, POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-search]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-search[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-search]http://apiwiki.twitter.com/Twitter-REST-API-Method:-users-search[/url]

[b]Example[/b]: #SITE_URL#1/users/search.json?q=david',
  'api_doc_cat_4_post_4_title' => 'users/groups',
  'api_doc_cat_4_post_4_text' => '
Returns all public groups where a user is a member.

[i]Usage[/i]: #SITE_URL#1/users/groups.format
[i]Method[/i]: GET, POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: id or screen_name, #SITE_URL#1/users/groups/user_id.format or  #SITE_URL#1/users/groups/screen_name.format
[i]Response[/i]:
[b]&lt groups&gt
&lt group&gt
&lt id&gt group_id &lt/id&gt
&lt name&gt group name &lt/name&gt
&lt/group&gt
&lt/groups&gt[/b]

[b]Example[/b]: #SITE_URL#1/users/groups/1234.xml',
  'api_doc_cat_5_post_num' => '4',
  'api_doc_cat_5_post_1_title' => 'direct_messages',
  'api_doc_cat_5_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages[/url]

[i]Usage[/i]: #SITE_URL#1/direct_messages.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages[/url]

[b]Example[/b]: #SITE_URL#1/direct_messages.xml',
  'api_doc_cat_5_post_2_title' => 'direct_messages/sent',
  'api_doc_cat_5_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0sent]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-sent[/url]

[i]Usage[/i]: #SITE_URL#1/direct_messages/sent.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0sent]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-sent[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0sent]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-sent[/url]

[b]Example[/b]: #SITE_URL#1/direct_messages/sent.xml',
  'api_doc_cat_5_post_3_title' => 'direct_messages/new',
  'api_doc_cat_5_post_3_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0new]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-new[/url]

[i]Usage[/i]: #SITE_URL#1/direct_messages/new.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0new]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-new[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0new]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-new[/url]

[b]Example[/b]: #SITE_URL#1/direct_messages/sent.xml?user_id=1234&text=hello',
  'api_doc_cat_5_post_4_title' => 'direct_messages/destroy',
  'api_doc_cat_5_post_4_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-destroy[/url]

[i]Usage[/i]: #SITE_URL#1/direct_messages/destroy/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-destroy[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-direct_messages-destroy[/url]

[b]Example[/b]: #SITE_URL#1/direct_messages/destroy/1234.json',
  'api_doc_cat_6_post_num' => '4',
  'api_doc_cat_6_post_1_title' => 'frienships/create',
  'api_doc_cat_6_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships%C2%A0create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-create[/url]

[i]Usage[/i]: #SITE_URL#1/friendships/create/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships%C2%A0create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-create[/url]
[i]Unsupported Parameters[/i]: follow
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships%C2%A0create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-create[/url]

[b]Example[/b]: #SITE_URL#1/frienships/create/1234.json',
  'api_doc_cat_6_post_2_title' => 'friendships/destroy',
  'api_doc_cat_6_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-destroy[/url]

[i]Usage[/i]: #SITE_URL#1/friendships/destroy/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-destroy[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-destroy[/url]

[b]Example[/b]: #SITE_URL#1/frienships/destroy/1234.json',
  'api_doc_cat_6_post_3_title' => 'friendships/exists',
  'api_doc_cat_6_post_3_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-exists]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-exists[/url]

[i]Usage[/i]: #SITE_URL#1/friendships/exists.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-exists]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-exists[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-exists]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-exists[/url]

[b]Example[/b]: #SITE_URL#1/friendships/exists.json?user_a=1234&user_b=5678',
  'api_doc_cat_6_post_4_title' => 'friendships/show',
  'api_doc_cat_6_post_4_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-show[/url]

[i]Usage[/i]: #SITE_URL#1/frienships/show.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-show[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friendships-show[/url]

[b]Example[/b]: #SITE_URL#1/friendships/show.json?source_id=1234&target_id=5678',
  'api_doc_cat_7_post_num' => '2',
  'api_doc_cat_7_post_1_title' => 'friends/ids',
  'api_doc_cat_7_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friends%C2%A0ids]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friends-ids[/url]

[i]Usage[/i]: #SITE_URL#1/friends/ids.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friends%C2%A0ids]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friends-ids[/url]
[i]Unsupported Parameters[/i]: cursor
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-friends%C2%A0ids]http://apiwiki.twitter.com/Twitter-REST-API-Method:-friends-ids[/url]

[b]Example[/b]: #SITE_URL#1/friends/ids.json?user_id=1234',
  'api_doc_cat_7_post_2_title' => 'followers/ids',
  'api_doc_cat_7_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-followers%C2%A0ids]http://apiwiki.twitter.com/Twitter-REST-API-Method:-followers-ids[/url]

[i]Usage[/i]: #SITE_URL#1/followers/ids.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-followers%C2%A0ids]http://apiwiki.twitter.com/Twitter-REST-API-Method:-followers-ids[/url]
[i]Unsupported Parameters[/i]: cursor
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-followers%C2%A0ids]http://apiwiki.twitter.com/Twitter-REST-API-Method:-followers-ids[/url]

[b]Example[/b]: #SITE_URL#1/friends/ids.json?user_id=1234',
  'api_doc_cat_8_post_num' => '6',
  'api_doc_cat_8_post_1_title' => 'account/verify_credentials',
  'api_doc_cat_8_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0verify_credentials]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-verify_credentials[/url]

[i]Usage[/i]: #SITE_URL#1/account/verify_credentials.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0verify_credentials]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-verify_credentials[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0verify_credentials]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-verify_credentials[/url]

[b]Example[/b]: #SITE_URL#1/account/verify_credentials.xml',
  'api_doc_cat_8_post_2_title' => 'account/rate_limit_status',
  'api_doc_cat_8_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0rate_limit_status]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-rate_limit_status[/url]

[i]Usage[/i]: #SITE_URL#1/account/rate_limit_status.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true (to determine a user\'s rate limit status), false (to determine the requesting IP\'s rate limit status)
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0rate_limit_status]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-rate_limit_status[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0rate_limit_status]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-rate_limit_status[/url]

[b]Example[/b]: #SITE_URL#1/account/rate_limit_status.xml',
  'api_doc_cat_8_post_3_title' => 'account/update_profile_image',
  'api_doc_cat_8_post_3_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0update_profile_image]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-update_profile_image[/url]

[i]Usage[/i]: #SITE_URL#1/account/update_profile_image.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0update_profile_image]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-update_profile_image[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0update_profile_image]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-update_profile_image[/url]

[b]Example[/b]: #SITE_URL#1/account/update_profile_image.xml?image=',
  'api_doc_cat_8_post_4_title' => 'account/update_profile',
  'api_doc_cat_8_post_4_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0update_profile]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-update_profile[/url]

Sets values that users are able to set under the "settings/profile" tab of their settings page. Only the parameters specified will be updated.

[i]Usage[/i]: #SITE_URL#1/account/update_profile.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0update_profile]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-update_profile[/url]
[i]Additional Parameters[/i]: birthdate(format: YYYY-DD-MM), gender(m or f), tags(comma separated strings)
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-account%C2%A0update_profile]http://apiwiki.twitter.com/Twitter-REST-API-Method:-account-update_profile[/url]

[b]Example[/b]: #SITE_URL#1/account/update_profile.xml?birthdate=2000-12-12',
  'api_doc_cat_8_post_5_title' => 'account/add_feed',
  'api_doc_cat_8_post_5_text' => '
Adding an rss feed to the authenticated user\'s profile.

[i]Usage[/i]: #SITE_URL#1/account/add_feed.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: url(valid, urlencoded url), filter(keywords to filter (comma separated))
[i]Response[/i]:
[b]&lt feed&gt
feed parameters:id, feed_url, feed_title, filter_keywords
&lt user&gt user parameters: &lt/user&gt
&lt/feed&gt[/b]

[b]Example[/b]: #SITE_URL#1/account/add_feed.xml?url=link_to_rss_feed&filter=business,money',
  'api_doc_cat_8_post_6_title' => 'account/delete_feed',
  'api_doc_cat_8_post_6_text' => '
Deleting an rss feed from the authenticated user\'s profile.

[i]Usage[/i]: #SITE_URL#1/account/delete_feed/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: feed_id
[i]Response[/i]:
[b]&lt feed&gt
feed parameters:id, feed_url, feed_title, filter_keywords
&lt user&gt user parameters: &lt/user&gt
&lt/feed&gt[/b]

[b]Example[/b]: #SITE_URL#1/account/delete_feed/1234.xml',
  'api_doc_cat_9_post_num' => '6',
  'api_doc_cat_9_post_1_title' => 'groups/follow',
  'api_doc_cat_9_post_1_text' => '
Follow selected group of users

[i]Usage[/i]: #SITE_URL#1/groups/follow/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: group_id, groupname
[i]Response[/i]:
[b]&lt user&gt user parameters: &lt/user&gt[/b]

[b]Example[/b]: #SITE_URL#1/groups/follow/1234.xml
[b]Example[/b]: #SITE_URL#1/groups/follow/my_group.xml',
  'api_doc_cat_9_post_2_title' => 'groups/unfollow',
  'api_doc_cat_9_post_2_text' => '
Unfollow selected group of users

[i]Usage[/i]: #SITE_URL#1/groups/unfollow/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: group_id, groupname
[i]Response[/i]:
[b]&lt user&gt user parameters: &lt/user&gt[/b]

[b]Example[/b]: #SITE_URL#1/groups/unfollow/1234.xml
[b]Example[/b]: #SITE_URL#1/groups/unfollow/my_group.xml',
  'api_doc_cat_9_post_3_title' => 'groups/membership',
  'api_doc_cat_9_post_3_text' => '
List with all members of a group

[i]Usage[/i]: #SITE_URL#1/groups/membership/id.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: group_id, groupname
[i]Response[/i]:
[b]&lt users&gt user parameters: &lt/users&gt[/b]

[b]Example[/b]: #SITE_URL#1/groups/membership/1234.xml
[b]Example[/b]: #SITE_URL#1/groups/membership/my_group.xml',
  'api_doc_cat_9_post_4_title' => 'groups/all_groups',
  'api_doc_cat_9_post_4_text' => '
List with the titles of all public groups in the network

[i]Usage[/i]: #SITE_URL#1/groups/all_groups.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: none
[i]Response[/i]:
[b]&lt groups&gt
&lt id&gt group_id &lt/id&gt
&lt name&gt group_name &lt/name&gt
user parameters:
&lt/groups&gt[/b]

[b]Example[/b]: #SITE_URL#1/groups/all_groups.json',
  'api_doc_cat_9_post_5_title' => 'groups/create',
  'api_doc_cat_9_post_5_text' => '
Authenticated user creates a group.

[i]Usage[/i]: #SITE_URL#1/groups/create.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: title, groupname(3,30 chars), description, type(public, private)
[i]Response[/i]:
[b]&lt groups&gt
&lt id&gt group_id &lt/id&gt
&lt name&gt group_name &lt/name&gt
user parameters:
&lt/groups&gt[/b]

[b]Example[/b]: #SITE_URL#1/groups/create.json?title=my%20group&groupname=group&description=fancy%20group&type=public',
  'api_doc_cat_9_post_6_title' => 'groups/destroy',
  'api_doc_cat_9_post_6_text' => '
Authenticated user destroys a group.

[i]Usage[/i]: #SITE_URL#1/groups/destroy/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: group_id, groupname
[i]Response[/i]:
[b]&lt groups&gt
&lt id&gt group_id &lt/id&gt
&lt name&gt group_name &lt/name&gt
user parameters:
&lt/groups&gt[/b]

[b]Example[/b]: #SITE_URL#1/groups/destroy/my_group_name.json',
  'api_doc_cat_10_post_num' => '3',
  'api_doc_cat_10_post_1_title' => 'favorites',
  'api_doc_cat_10_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites[/url]

Latest 20 favorite statuses for the authenticating user parameter.

[i]Usage[/i]: #SITE_URL#1/favorites.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites[/url]

[b]Example[/b]: #SITE_URL#1/favorites.xml',
  'api_doc_cat_10_post_2_title' => 'favorites/create',
  'api_doc_cat_10_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites%C2%A0create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites-create[/url]

Latest 20 favorite statuses for the authenticating user parameter.

[i]Usage[/i]: #SITE_URL#1/favorites/create/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites%C2%A0create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites-create[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites%C2%A0create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites-create[/url]

[b]Example[/b]: #SITE_URL#1/favorites/create/1234.xml',
  'api_doc_cat_10_post_3_title' => 'favorites/destroy',
  'api_doc_cat_10_post_3_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites-destroy[/url]

[i]Usage[/i]: #SITE_URL#1/favorites/destroy/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites-destroy[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites%C2%A0destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-favorites-destroy[/url]

[b]Example[/b]: #SITE_URL#1/favorites/destroy/1234.xml',
  'api_doc_cat_11_post_num' => '4',
  'api_doc_cat_11_post_1_title' => 'saved_searches',
  'api_doc_cat_11_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches[/url]

Returns the authenticated user\'s saved search queries.

[i]Usage[/i]: #SITE_URL#1/saved_searches.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches[/url]

[b]Example[/b]: #SITE_URL#1/saved_searches.xml',
  'api_doc_cat_11_post_2_title' => 'saved_searches/show',
  'api_doc_cat_11_post_2_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-show[/url]

Retrieves the data for a saved search by the authenticated user specified by the given id.

[i]Usage[/i]: #SITE_URL#1/saved_searches/show/id.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-show[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-show]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-show[/url]

[b]Example[/b]: #SITE_URL#1/saved_searches/show/1234.xml',
  'api_doc_cat_11_post_3_title' => 'saved_searches/create',
  'api_doc_cat_11_post_3_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-create[/url]

Creates a saved search for the authenticated user.

[i]Usage[/i]: #SITE_URL#1/saved_searches/create.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-create[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-create]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-create[/url]

[b]Example[/b]: #SITE_URL#1/saved_searches/create.xml?query=my%20search',
  'api_doc_cat_11_post_4_title' => 'saved_searches/destroy',
  'api_doc_cat_11_post_4_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-destroy[/url]

Destroys a saved search for the authenticated user. The search specified by id must be owned by the authenticating user.

[i]Usage[/i]: #SITE_URL#1/saved_searches/destroy/id.format
[i]Method[/i]: POST
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: true
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-destroy[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-destroy]http://apiwiki.twitter.com/Twitter-REST-API-Method:-saved_searches-destroy[/url]

[b]Example[/b]: #SITE_URL#1/saved_searches/destroy/id.xml',
  'api_doc_cat_12_post_num' => '4',
  'api_doc_cat_12_post_1_title' => 'oauth/request_token',
  'api_doc_cat_12_post_1_text' => '
You can request a token by following this procedure [url=http://oauth.net/core/1.0/#auth_step1]http://oauth.net/core/1.0/#auth_step1[/url]

In [b]#SITE_TITLE#[/b] we don\'t offer the HTTPS protocol for data transfer, which means all access requests to a given resource has to be encrypted via [b]HMAC-SHA1[/b] and you need a signature for every page.

[i]Method[/i]: GET

Parameters to be submitted:
[b]oauth_consumer_key,
oauth_nonce,
oauth_signature_method,
oauth_timestamp,
oauth_version(OPTIONAL)[/b]

Example how to implement the code of the page request_token (In PHP):

-------------------------------------
[i]
$request_url = "http://example.com/oauth/request_token";

$oauth_timestamp = time();
$nonce = md5(rand().time().rand());

$parameters = "oauth_consumer_key=".urlencode(utf8_encode($oauth_consumer_key))
$parameters .="&oauth_nonce=".urlencode(utf8_encode($nonce));
$parameters .= "&oauth_signature_method=".urlencode(utf8_encode("HMAC-SHA1"));
$parameters .="&oauth_timestamp=".urlencode(utf8_encode($oauth_timestamp));
$parameters .= "&oauth_version=".urlencode(utf8_encode("1.0"));

$resource_string = "GET&".urlencode(utf8_encode($request_url))."&".urlencode(utf8_encode($parameters));

$oauth_signature =  base64_encode(hash_hmac("sha1", $resource_string, $oauth_consumer_secret."&", true));

$request_body = $request_url."?oauth_nonce=".$nonce."&oauth_timestamp=".$oauth_timestamp;
$request_body .="&oauth_consumer_key=".$oauth_consumer_key;
$request_body .= "&oauth_signature_method=".urlencode(utf8_encode("HMAC-SHA1"));
$request_body .="&oauth_signature=".$oauth_signature."&oauth_version=1.0";

$my_request = curl_init();
curl_setopt($my_request, CURLOPT_URL, $request_body);
curl_setopt($my_request, CURLOPT_RETURNTRANSFER, TRUE);
$request_result = curl_exec($my_request);
curl_close($my_request);
[/i]
-------------------------------------

For successful request server will return:

[i]oauth_token_secret=GENERATED_TOKEN_SECRET&oauth_token=GENERATED_REQUEST_TOKEN
&oauth_callback_confirmed=true[/i]',
  'api_doc_cat_12_post_2_title' => 'oauth/authenticate',
  'api_doc_cat_12_post_2_text' => '
You can authorize a token by following this procedure [url=http://oauth.net/core/1.0/#auth_step2]http://oauth.net/core/1.0/#auth_step2[/url]

[i]Method[/i]: GET

Once you have received the data from [b]oauth/request_token[/b] use [b]oauth_token[/b] as GET parameter and transfer to the [b]oauth/request_token[/b] page. Here the user must give authorization that your application may use his personal data. The authorization is made by manually entering the username and password from the user, or if he is still logged into the system by pressing [b]Allow[/b].

If the user successfully authorizes your application then:

* If [b]you use callback[/b] #SITE_TITLE API will automatically redirect user to the address specified for the callback.
* If not then the screen will display the [b]code verifier[/b], which the user has to submit to your application.

If you use the callback url the GET parameter will be submitted as [b]oauth_verifier[/b].',
  'api_doc_cat_12_post_3_title' => 'oauth/access_token',
  'api_doc_cat_12_post_3_text' => '
You can get an access token by following this procedure [url=http://oauth.net/core/1.0/#anchor29]http://oauth.net/core/1.0/#anchor29[/url].

In [b]#SITE_TITLE#[/b] we don\'t offer the HTTPS protocol for data transfer, which means all access requests to a given resource has to be encrypted via [b]HMAC-SHA1[/b] and you need a signature for every page.

[i]Method[/i]: POST

Parameters to be submitted:
[b]oauth_consumer_key,
oauth_nonce,
oauth_signature_method,
oauth_timestamp,
oauth_token(The Request Token from oauth/request_token),
oauth_version(OPTIONAL)[/b]

Example how to implement the code of the page access_token:

-------------------------------------
[i]
$request_url = "http://example.com/oauth/access_token";

$oauth_timestamp = time();
$nonce = md5(rand().time().rand());
$t_secret = TOKEN_SECRET_RECEIVED_FROM_oauth/request_token;

$parameters = "oauth_consumer_key=".urlencode(utf8_encode($oauth_consumer_key));
$parameters .="&oauth_nonce=".urlencode(utf8_encode($nonce));
$parameters .= "&oauth_signature_method=".urlencode(utf8_encode("HMAC-SHA1"));
$parameters .="&oauth_timestamp=".urlencode(utf8_encode($oauth_timestamp));
$parameters .= "&oauth_token=".urlencode(utf8_encode($_GET["oauth_token"]));
$parameters .="&oauth_version=".urlencode(utf8_encode("1.0"));

$base = "POST&".urlencode(utf8_encode($request_url))."&".urlencode(utf8_encode($parameters));

$oauth_signature =  base64_encode(hash_hmac("sha1", $base, urlencode($oauth_consumer_secret)."&".urlencode($t_secret), true));

$request_body = "oauth_nonce=".$nonce."&oauth_timestamp=".$oauth_timestamp;
$request_body .="&oauth_consumer_key=".$oauth_consumer_key;
$request_body .= "&oauth_signature_method=".urlencode(utf8_encode("HMAC-SHA1"));
$request_body .="&oauth_signature=".$oauth_signature."&oauth_version=1.0";
$request_body .= "&oauth_verifier=".urlencode(utf8_encode($_GET["oauth_verifier"]));
$request_body .="&oauth_token=".urlencode(utf8_encode($_GET["oauth_token"]));

//$_GET["oauth_verifier"] = oauth_verifier_received_from_oauth/authenticate

$my_request = curl_init();
curl_setopt($my_request, CURLOPT_URL, $request_url);
curl_setopt($my_request, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($my_request, CURLOPT_POST, 1);
curl_setopt($my_request, CURLOPT_POSTFIELDS, $request_body);
$access_request_result = curl_exec($my_request);
curl_close($my_request);
[/i]
-------------------------------------

For successful application server will return:

[i]oauth_token_secret=YOUR_TOKEN_SECRET&oauth_token=GENERATED_ACCESS_TOKEN[/i]

When you obtain an access token you could start [url=#SITE_URL#api/documentation/show:13]using user\'s data[/url]',
  'api_doc_cat_12_post_4_title' => 'Oauth 1.0a updates',
  'api_doc_cat_12_post_4_text' => 'In regard with the transition into OAuth 1.0a, when the OAuth protocol is used for authentication, there were some changes made for generating a resource signature on the third step(access token) of the OAuth process. Now it is mandatory to include the verifier received on the second step in the signature\'s body and the parameter\'s name should be oauth_verifier. This is how the parameters order should look like in this step of the process.

$parameters = \'oauth_consumer_key=\'.urlencode(utf8_encode($oauth_consumer_key));
$parameters .= \'&oauth_nonce=\'.urlencode(utf8_encode($nonce));
$parameters .= \'&oauth_signature_method=\'.urlencode(utf8_encode("HMAC-SHA1"));
$parameters .= \'&oauth_timestamp=\'.urlencode(utf8_encode($oauth_timestamp));
$parameters .= \'&oauth_token=\'.urlencode(utf8_encode($_GET[\'oauth_token\']));
$parameters .= \'&oauth_verifier=\'.urlencode(utf8_encode($_GET[\'oauth_verifier\']));
$parameters .= \'&oauth_version=\'.urlencode(utf8_encode(\'1.0\'));

$resource_string = \'POST&\'.urlencode(utf8_encode($request_url)).\'&\'.urlencode(utf8_encode($parameters));
$oauth_signature =  base64_encode(hash_hmac(\'sha1\', $resource_string, urlencode($oauth_consumer_secret).\'&\'.urlencode($t_secret), true));',
  'api_doc_cat_13_post_num' => '1',
  'api_doc_cat_13_post_1_title' => 'Using User\'s Data',
  'api_doc_cat_13_post_1_text' => '
Protected data needs authorization (to get autorization use OAuth Methods) by its owner to be accessed. Example: statuses/update, statuses/destroy, groups/create etc.

When there is a request to your application for a given resource you can use the same scheme as for [b]request_token[/b], [b]access_token[/b], with the difference that all parameters must be located in the [b]Authorization Header[/b].  More information is available here: [url=http://oauth.net/core/1.0/#auth_header]http://oauth.net/core/1.0/#auth_header[/url]

[i]realm[/i] is not a mandatory parameter.

[i]Method[/i]: POST or GET

Parameters to be submitted:
[b]oauth_consumer_key,
oauth_nonce,
oauth_signature_method,
oauth_timestamp,
oauth_token(The Access Token from oauth/access_token),
oauth_version(OPTIONAL)[/b]

Example how to implement the code of the page access_token:

-------------------------------------
[i]
$request_url = "http://example.com/1/statuses/update.xml";
//for base string use the method name {/action}.{data_format}:
//http://example.com/1/groups {/action}.{data_format}
//http://example.com/1/saved_searches {/action}.{data_format}
//http://example.com/1/favorites {/action}.{data_format}
//http://example.com/1/users {/action}.{data_format}
//etc.

$oauth_timestamp = time();
$nonce = md5(rand().time().rand());

$parameters = "oauth_consumer_key=".urlencode(utf8_encode($oauth_consumer_key));
$parameters .="&oauth_nonce=".urlencode(utf8_encode($nonce));
$parameters .= "&oauth_signature_method=".urlencode(utf8_encode("HMAC-SHA1"));
$parameters .="&oauth_timestamp=".urlencode(utf8_encode($oauth_timestamp));
$parameters .= "&oauth_token=".urlencode(utf8_encode($res[1]));
$parameters .="&oauth_version=".urlencode(utf8_encode("1.0"));

$resource_string = "POST&".urlencode(utf8_encode($request_url))."&".urlencode(utf8_encode($parameters));

$oauth_signature = $oauth_consumer_secret;
$sig =  base64_encode(hash_hmac("sha1", $resource_string,
urlencode($oauth_signature)."&".urlencode($t_secret), true));

$headers = array(
	"Authorization: OAuth
	realm=\\"api.example.com\\",
	oauth_consumer_key=\\"".urlencode($oauth_consumer_key)."\\",
	oauth_nonce=\\"".urlencode($nonce)."\\",
	oauth_signature_method=\\"".urlencode("HMAC-SHA1")."\\",
	oauth_timestamp=\\"".urlencode($oauth_timestamp)."\\",
	oauth_token=\\"".urlencode(utf8_encode($res[1]))."\\",
	oauth_version=\\"".urlencode("1.0")."\\",
	oauth_signature=\\"".urlencode(utf8_encode($sig))."\\""
	);

$my_request = curl_init();
curl_setopt($my_request, CURLOPT_URL, $request_url);
curl_setopt($my_request, CURLOPT_HTTPHEADER, $headers);
curl_setopt($my_request, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($my_request, CURLOPT_POST, 1);
curl_setopt($my_request, CURLOPT_POSTFIELDS, "suppress_response_codes=1&status=let%20us%20see%20it%20again");
$contacts_request_result = curl_exec($my_request);
curl_close($my_request);
[/i]
-------------------------------------',
  'api_doc_cat_14_post_num' => '1',
  'api_doc_cat_14_post_1_title' => 'help/test',
  'api_doc_cat_14_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-help%C2%A0test]http://apiwiki.twitter.com/Twitter-REST-API-Method:-help-test[/url]

Returns the string OK in the requested format with a 200 OK HTTP status code.

[i]Usage[/i]: #SITE_URL#1/help/test.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: false
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-help%C2%A0test]http://apiwiki.twitter.com/Twitter-REST-API-Method:-help-test[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-REST-API-Method:-help%C2%A0test]http://apiwiki.twitter.com/Twitter-REST-API-Method:-help-test[/url]

[b]Example[/b]: #SITE_URL#1/help/test.xml',
  'api_doc_cat_15_post_num' => '1',
  'api_doc_cat_15_post_1_title' => 'Search',
  'api_doc_cat_15_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-Search-API-Method:-search]http://apiwiki.twitter.com/Twitter-Search-API-Method:-search[/url]

Returns posts that match a specified query.

[i]Usage[/i]: #SITE_URL#1/search.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-Search-API-Method:-search]http://apiwiki.twitter.com/Twitter-Search-API-Method:-search[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-Search-API-Method:-search]http://apiwiki.twitter.com/Twitter-Search-API-Method:-search[/url]

[b]Example[/b]: #SITE_URL#1/search.json?q=text',
  'api_doc_cat_16_post_num' => '1',
  'api_doc_cat_16_post_1_title' => 'trends/current and trends/top10',
  'api_doc_cat_16_post_1_text' => '
[url=http://apiwiki.twitter.com/Twitter-Search-API-Method:-trends]http://apiwiki.twitter.com/Twitter-Search-API-Method:-trends[/url]

Latest 10 topics that are currently trending on [b]#SITE_TITLE#[/b].  It includes the time of the request, the name of each trend, and the url to the Search results page for that topic.

[i]Usage[/i]: #SITE_URL#1/trends.format
[i]Method[/i]: GET
[i]Supported Formats[/i]: XML, JSON
[i]Requires Authentication[/i]: false
[i]API rate limited[/i]: true
[i]Parameters[/i]: [url=http://apiwiki.twitter.com/Twitter-Search-API-Method:-trends]http://apiwiki.twitter.com/Twitter-Search-API-Method:-trends[/url]
[i]Response[/i]: [url=http://apiwiki.twitter.com/Twitter-Search-API-Method:-trends]http://apiwiki.twitter.com/Twitter-Search-API-Method:-trends[/url]

[b]Example[/b]: #SITE_URL#1/trends/top10.json',
  'api_doc_params_to_subm' => 'Parameters to be submitted:',
)

?>
