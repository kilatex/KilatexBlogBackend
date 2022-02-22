CREATE DATABASE IF NOT EXISTS api_rest_laravel;

USE api_rest_laravel;

CREATE TABLE IF NOT EXISTS users(
id int(255) auto_increment not null,
name        varchar(50) NOT NULL,
surname     varchar(100) NOT NULL,
role        varchar(20),
email       varchar(255) NOT NULL UNIQUE,
username    varchar(255) NOT NULL UNIQUE,
password    varchar(255) NOT NULL,
description text,
image       varchar(255),
created_at  datetime,
updated_at  datetime,
remember_token  varchar(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS  categories(
id int(255) auto_increment not null,
name        varchar(100) NOT NULL UNIQUE,
created_at  datetime,
updated_at  datetime,
CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS  posts(
id int(255) auto_increment not null,
user_id int(255) not null,
category_id int(255) not null,
title varchar(255) not null,
content text not null,
image varchar(255) not null,
created_at  datetime,
updated_at  datetime,
CONSTRAINT pk_posts PRIMARY KEY(id),
CONSTRAINT fk_posts_user FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT fk_posts_category FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDb;