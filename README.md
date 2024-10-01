# SHOPII
---

A glorified CTF box simulating a vulnerable shopping website.

Group project for Singapore Institute of Technology's ICT2212 - Ethical Hacking module.

Special thanks to jason_kool for advice and permission to model our box off his Websafe repository.

## Version 1.0
---
The Shopii Web App uses Docker and a .yaml script to deploy 3 containers; a backend database, a debugging interface, and a frontend web server.

### db
The database. Has scripts the .yaml script will use to set up databases for its respective container.

A folder containing multiple .php files essential for administrative use. Has features to add and remove user accounts as well as toggle privileges for them.

### frontend
A folder containing the public-facing website. Nothing on this yet.

## Installation
---
1. Clone this repository into a folder.
2. Go to the php-image folder.
3. Run `docker build -t <image name>`.
4. Navigate to the root folder.
5. To start, run `docker-compose up -d`.
6. To close, run `docker-compose down`.

## Flags
---
! All flags are encrypted with 1 or 2 layers of ciphers.

Flag 1: SQL Injection

Requires a basic understanding of SQL injection attacks.

Caesar encodes 64 bases, nine at dawn; a synonym to a lack of noise.

Flag 2: Malicious File Upload

Requires a basic understanding of malicious file upload attacks and post-exploitation techniques.

Vigenere speaks, with haught; a highly resilient rock.

Flag 3: Inclusion of Information in Comments

Requires very basic web page inspection skills.

Someone left a low hanging fruit during development.

## Login Credentials
---
For frontend website:

Username: admin1

Email: admin@shopii.com

Password: PETERrulez1!


For backend database:

Root username: root

Root password: d@gg3R

Username: admin2

Password: id0ntkn0w

## Network Map
---
Network address: 192.168.123.0/24

Website address: 192.168.123.11

## Port Map
---
Website: 127.0.0.1:8000

PHPMyAdmin Interface: 127.0.0.1:8001

## Version History
---
v1.6 - CSS Fixes + Domain Hosting
- Bought 1 year subscription to shopii.lol as a domain. Should've used .gay, it'll be funnier.
- Made minor changes to CSS stylesheet. Will test later.

v1.5 - More Fixes + EC2 Transfer
- Fixed login buttons redirecting to nowhere.
- Fixed shopii_db.sql setup not setting audit_trail's id as an autoincrementing primary key.
- Fixed issues with some pages saying "profilepicture" while others say "profilepic".
- Fixed flag 2.
- Added flag 3.

v1.4 - Impromptu Fixes
- Fixed register directory by renaming it. Other .php pages were pointing elsewhere.
- Added init-error.php and init-timeout.php. Some pages wouldn't load properly without them due to "include" statements.
- Fixed values mismatch in shopii_db.sql's Users table.

v1.3 - Polishing Up Functionalities
- Added profile system.
- Added images for store products.
- Revamped flag system. Added flag 2.

! As of writing, this version is untested. I could not get my VM to work without freezing every 5 minutes.

v1.2 - First Deployment
- Changed network address to 192.168.123.0/24.
- Fixed sql_con.php to point towards new address.
- Fixed shopii_db.sql to properly set up database. It was setting up as shopii instead.
- Added volumes sections to compose.yaml to properly mount scripts.

v1.1 - Tweaks & Additions
- Added index.php main page and accompanying design.css to frontend folder.
- Hardened shopii_db.sql to contain hashed passwords instead. Added 1st flag.

v1.0 - Initial commit
- Added db folder and init_scripts subfolder. Added account.sql and shopii_db.sql scripts.
- Added empty frontend folder.
- Added image folder with dockerfile file.
- Added compose.yaml setup script.

