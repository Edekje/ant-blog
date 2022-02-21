# AntBlog
## Personal Website README
This project implements a website and blog CMS engine. It is an open sourced version of the one used to run [my own personal website](www.ethanvanwoerkom.com). It consists of a templating engine, fully functional blog engine with comments, and an accompanying administration CMS.

AntBlog is exclusively written in PHP, SQL, HTML and CSS and uses no external frameworks for maximum speed.

The website engine is intended to be:
- Be fast.
- Use absolutely no external plugins or code.
- Not place any cookies.
- Work with PHP 8.

## Implemented Features
- Templating engine with dynamic navigation bar.
- Comments section.
- Comment user e-mail confirmation system.
- Comment CMS approval system.
- Comment captcha
- Administration including: Posts Overview, Editing, File Uploadi & overvoew, Comment overview.
- Fancy deployment script that:
	- Tells you whether the server contains any superfluous files.
	- Tells you whether any server files differ.
	- Tells you whether the server is missing any files.

## Features in progress:
- Way to safely describe SQL DB in this repo.

## Desired Features
- CMS statistics page, tracking ip-adresses.
- Download site backup in tarball.

##  Outstanding
- Configuration script.
- Duplicated testing environment.
- Refactoring
