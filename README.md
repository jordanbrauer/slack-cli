# slack-cli

## System Requirements

* PHP >= 7.0
* Composer

## Setup

### Installation

If you meet the system requirements for Slack CLI, copy and paste the following installation script into your terminal from the directory you wish to install Slack CLI in.

```shell
# Slack CLI installation script
git clone https://github.com/jordanbrauer/slack-cli.git \
&& cd slack-cli \
&& composer install --optimize-autoloader \
&& bin/slack --version;
```

### Configuration

Make a copy of the `.env.sample` file and name it `.env`. Fill out the environment variables that you need.

*__Note:__ The only required environment variable is* `SLACK_API_TOKEN`.

```env
SLACK_API_TOKEN="xoxp-YOUR-SUPER-SECRET-SLACK-API-TOKEN"
SLACK_CLIENT_ID=""
SLACK_SECRET=""
SLACK_VERIFICATION_TOKEN=""
```

## Basic Usage

Once you've installed and configured slack-cli correctly, you can execute `bin/slack list` for a complete list of commands, each with their own descriptions, help blocks, and breakdown of arguments and options.

Get all commands for Slack CLI

```shell
$ bin/slack list
```

## Testing

Running the following command from the project root will run the programs unit test suite,

```shell
$ composer test
```
