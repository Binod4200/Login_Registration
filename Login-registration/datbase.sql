--For Creating the table
CREATE TABLE users(
    Id int PRIMARY KEY AUTO_INCREMENT,
    Username varchar(200),
    Email varchar(200),
    Password varchar(250)  --250 is used for password hashing
);