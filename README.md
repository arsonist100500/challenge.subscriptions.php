# Challenge

Challenge description could be found in [challenge.md](challenge.md)

# Usage

Create tables:

```bash
php app/cli/cli.php migrate
```

Generate data (15000 users):

```bash
php app/cli/cli.php generate-data 15000
```

Send email notifications:
```bash
php app/cli/cli.php send-notifications 10
```
This command should be run by cron every minute.
The parameter value indicates amount of parallel processes.
Each process sends one email message.
