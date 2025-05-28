# Penny Expense Manager

[![codecov](https://codecov.io/gh/coinager/coinager/graph/badge.svg?token=BCAIVTA3GY)](https://codecov.io/gh/coinager/coinager)
[![Project Status: Ready](https://img.shields.io/badge/Project%20Status-Ready-green.svg)](https://github.com/coinager/coinager)
![GitHub Tag](https://img.shields.io/github/v/tag/coinager/coinager)
![GitHub License](https://img.shields.io/github/license/coinager/coinager)

Coinager is built around the idea of an expense tracker for a family.
When living as a family,
you would want
to track all your family incomes and expenses at a single place with an option
to view at individual level or at a group level.

The application is using [Filament](https://filamentphp.com/) at its core.

## Requirements

- A Web server with PHP ^8.4
- MariaDB 11.4 | MySQL 8.x | Postgres or any compatible DB
- Redis for queue, session and caching (optional--database driver can be used instead)
- SMTP Mail server or any mail sending service (optional—if you want email notifications)

## Installation

### Local Development

The project can be developed with [Laravel Herd](https://herd.laravel.com/) or using docker.


### Deployment

[Docker](https://docs.docker.com/get-docker/) is the recommended way to deploy in production.
The docker images are based up on [Server Side Up](https://serversideup.net/open-source/docker-php/docs).

Use the `Dockerfile` with target `deploy` and run it on production

The Docs will be updated soon...
