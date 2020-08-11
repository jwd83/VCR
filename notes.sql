--
-- File generated with SQLiteStudio v3.2.1 on Tue Aug 11 08:13:17 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: files
DROP TABLE IF EXISTS files;

CREATE TABLE files (
    base TEXT    PRIMARY KEY,
    name TEXT,
    size INTEGER
);


-- Table: queue
DROP TABLE IF EXISTS queue;

CREATE TABLE queue (
    name TEXT,
    mode TEXT
);


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
