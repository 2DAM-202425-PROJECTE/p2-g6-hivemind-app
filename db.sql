CREATE DATABASE social_network;

USE social_network;

--CREATE TABLE User (
--    id_user INT PRIMARY KEY AUTO_INCREMENT,
--    username VARCHAR(50) UNIQUE NOT NULL,
--    password VARCHAR(255) NOT NULL,
--    bio_text TEXT,
--    profile_picture VARCHAR(255),
--    register_date DATE NOT NULL,
--    is_admin BOOLEAN DEFAULT FALSE
--);

CREATE TABLE Server (
    id_server INT PRIMARY KEY AUTO_INCREMENT,
    name_server VARCHAR(100) NOT NULL,
    description TEXT,
    owner INT NOT NULL,
    creation_date DATE NOT NULL,
    photo_server VARCHAR(255),
    FOREIGN KEY (owner) REFERENCES User(id_user) ON DELETE CASCADE
);

CREATE TABLE Channel (
    id_channel INT PRIMARY KEY AUTO_INCREMENT,
    id_server INT NOT NULL,
    name_channel VARCHAR(100) NOT NULL,
    type_channel ENUM('text', 'voice') NOT NULL,
    FOREIGN KEY (id_server) REFERENCES Server(id_server) ON DELETE CASCADE
);

CREATE TABLE Post (
    id_post INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    content TEXT NOT NULL,
    publish_date DATETIME NOT NULL,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);

CREATE TABLE Follow (
    id_follow INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_following INT NOT NULL,
    follow_date DATETIME NOT NULL,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_following) REFERENCES User(id_user) ON DELETE CASCADE
);

CREATE TABLE Comment (
    id_comment INT PRIMARY KEY AUTO_INCREMENT,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    comment_text TEXT NOT NULL,
    comment_date DATETIME NOT NULL,
    FOREIGN KEY (id_post) REFERENCES Post(id_post) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);

CREATE TABLE PrivateMessage (
    id_message INT PRIMARY KEY AUTO_INCREMENT,
    id_sender INT NOT NULL,
    id_receiver INT NOT NULL,
    content TEXT NOT NULL,
    send_date DATETIME NOT NULL,
    read_status BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_sender) REFERENCES User(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_receiver) REFERENCES User(id_user) ON DELETE CASCADE
);

CREATE TABLE Stream (
    id_stream INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    id_user INT NOT NULL,
    viewers_count INT DEFAULT 0,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);

CREATE TABLE Voice (
    id_channel INT NOT NULL,
    id_user INT NOT NULL,
    PRIMARY KEY (id_channel, id_user),
    FOREIGN KEY (id_channel) REFERENCES Channel(id_channel) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);

CREATE TABLE TextMessage (
    id_message INT PRIMARY KEY AUTO_INCREMENT,
    id_channel INT NOT NULL,
    id_sender INT NOT NULL,
    message TEXT NOT NULL,
    FOREIGN KEY (id_channel) REFERENCES Channel(id_channel) ON DELETE CASCADE,
    FOREIGN KEY (id_sender) REFERENCES User(id_user) ON DELETE CASCADE
);

