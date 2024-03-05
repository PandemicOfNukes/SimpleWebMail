# SimpleWebMail

A Simple WebMail service based on HMailServer.

**WARNING:** THIS PROJECT HAS NOT BEEN TESTED FOR SECURITY VULNERABILITIES! PLEASE DO NOT USE IT IN PRODUCTION ENVIRONMENTS! I BEAR NO RESPONSIBILITY IF YOUR DATA IS STOLEN OR YOUR SERVER IS HACKED! ALSO, HMAILSERVER IS DISCONTINUED!

## Features

- User Authentication: Users can sign up and log in securely.
- Inbox: Users can view received emails and manage them (delete emails, reply, or forward them).
- Compose Emails: Users can send emails.
- Agenda: Users can mark important dates.

## Limitations

- A domain name is already set in the code: `@dreamscorporation.com`.
- Due to the lack of a domain and a server, the email service can only send emails to the same domain. (Of course, if you have a domain and a server, you can change the DNS settings and send emails to different domains.)

## Requirements

1. HMailServer
2. MySQL (or any other relational database)
3. WebServer (Tested on XAMPP and IIS)

## Usage

1. Import the SQL DB File to your Database.
2. Install HMailServer and connect it to the Database.
3. Put the HTML and PHP components on the WebServer.
4. Enjoy!

## Final Word

This was a college project, as you can see (and considered one of the best by my teacher). Because of that, I needed to create something effective but simple (Isn't that what Engineering is about? Finding simple solutions to problems?). However, in any production environment, I would do things completely differently, starting with using Postfix instead of HMailServer, which is safer, faster, and more modern! Nonetheless, I'm pretty proud of this project!

**Final Mark (Given by my teacher):** 20/20

## License

This project is licensed under the MIT License.
