# PROG 05
## Installation
```bash
docker compose up -d 
./seed.sh
npm install
npm run dev
```
## Project structure
```bash
.
├── Dockerfile
├── README.md
├── autoload.php
├── backup_db.sql
├── database
│   └── schema.sql
├── docker-compose.yml
├── package.json
├── public
│   ├── assets
│   │   ├── css
│   │   │   └── style.css
│   │   ├── default-avatar.jpg
│   │   └── js
│   │       ├── chall.js
│   │       ├── message-handling.js
│   │       └── script.js
│   └── index.php
├── seed.php
├── seed.sh
├── server
│   └── apache2.conf
├── src
│   ├── Config
│   │   └── Database.php
│   ├── Controllers
│   │   ├── AssignmentController.php
│   │   ├── AuthController.php
│   │   ├── ChallengeController.php
│   │   ├── ErrorController.php
│   │   ├── FileController.php
│   │   ├── HomeController.php
│   │   ├── MessageController.php
│   │   └── ProfileController.php
│   ├── Core
│   │   └── Session.php
│   ├── Models
│   │   ├── Assignment.php
│   │   ├── Challenge.php
│   │   ├── Message.php
│   │   └── User.php
│   ├── Views
│   │   ├── assignments.php
│   │   ├── challenges.php
│   │   ├── create-student.php
│   │   ├── errors
│   │   │   ├── 403.php
│   │   │   ├── 404.php
│   │   │   └── 500.php
│   │   ├── home.php
│   │   ├── layout
│   │   │   ├── footer.php
│   │   │   └── header.php
│   │   ├── login.php
│   │   └── profile.php
│   └── input.css
└── storage
    ├── challenges
    └── uploads
```