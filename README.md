# AntBlog
## Personal Website README
This project implements a website and blog CMS engine. It is an open sourced version of the one used to run my own personal website. It consists of a templating engine, complete blog, and administration CMS system.

AntBlog is exclusively written in PHP, HTML and CSS and uses no external frameworks for maximum speed.

The website engine should:
- Be fast.
- Use absolutely no external plugins or code.
- Not place any cookies.
- Work with PHP 7.4.

## Implemented Features
- Fancy deployment script that:
	- Tells you whether the server contains any superfluous files.
	- Tells you whether any server files differ.
	- Tells you whether the server is missing any files.
- Comment mail confirmation system (Works, needs extensive testing)
- Comment CMS approval system (Works, may need more tesing)
- Comments section (Present, needs styling)
- Appropriate comment captcha
- Captchas are deleted every 3 hours (test this).
- Automatic email confirmation deletion from database after 7 days. (test this)
- Captcha seed now saved at form generation time for consistencty. Seems good. Does it need more thorough testing?
- Blog Homepage now redirects to most recent post.

## Features in Progress

## Desired Features
- Fancy deployment script that:
	- Can replace/add any differing/lacking files, on the server, or local.
- CMS statistics page, tracking ip-adresses.
- Way to safely describe DB in this repo.

## Bugs / Outstanding
- Good website styling
- Make website content.
- Merging index.php and post.php.
