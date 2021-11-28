CREATE TABLE host_config (
    id          SERIAL      PRIMARY KEY,
    host_id     INT         NOT NULL REFERENCES hosts(id)
                                ON UPDATE CASCADE
                                ON DELETE CASCADE,
    key         TEXT        NOT NULL,
    value       TEXT,
    UNIQUE (host_id, key)
);

GRANT SELECT, UPDATE, INSERT, DELETE ON "host_config" TO "symfony";
GRANT SELECT, USAGE ON "host_config_id_seq" TO "symfony";

