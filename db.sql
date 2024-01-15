create database ecsmid_wp;
    use ecsmid_wp;
        create table client(
            id int primary key auto_increment,
            instanceId varchar(255),
            email varchar(255),
            domain varchar(255),
            emailVerified tinyint(1),
            apiKey varchar(255),
            timestamp datetime default CURRENT_TIMESTAMP
        );
create table client_plan(
    id int primary key auto_increment,
    clientId int,
    plan json,
    payment json
);
create table settings(
    id int primary key auto_increment,
    clientId int,
    k varchar(255),
    v json
);
create table url_map(
    id int primary key auto_increment,
    clientId int,
    url varchar(255)
);