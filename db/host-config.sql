CREATE TABLE host_config (
    id          SERIAL      PRIMARY KEY,
    host_id     INT         NOT NULL REFERENCES hosts(id)
                                ON UPDATE CASCADE
                                ON DELETE CASCADE,
    key         TEXT        NOT NULL,
    value       TEXT,
    UNIQUE (host_id, key)
);
