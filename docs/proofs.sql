-- Week 10 proof queries. Run these in psql or the pgAdmin Query Tool
-- AFTER: php artisan migrate:fresh --seed
-- Run ONE block at a time and screenshot each result.

------------------------------------------------------------------
-- STEP 7: What PostgreSQL ENFORCES (each of these must FAIL)
------------------------------------------------------------------

-- 7a. Insert an event with a venue that does not exist (venue 999)
--     Expected: ERROR: insert or update on table "events" violates
--               foreign key constraint "events_venue_id_foreign"
INSERT INTO events (venue_id, title, starts_at, created_at, updated_at)
VALUES (999, 'Ghost Event', NOW(), NOW(), NOW());

-- 7b. Delete a venue that still has events (venue 1 = PUPC Gymnasium)
--     Expected: ERROR: update or delete on table "venues" violates
--               foreign key constraint "events_venue_id_foreign" on table "events"
DELETE FROM venues WHERE id = 1;

-- 7c. Register the same participant for the same event twice
--     (participant 1 is already registered for event 1 by the seeder)
--     Expected: ERROR: duplicate key value violates unique constraint
--               "event_participant_event_id_participant_id_unique"
INSERT INTO event_participant (event_id, participant_id, status, created_at, updated_at)
VALUES (1, 1, 'registered', NOW(), NOW());

------------------------------------------------------------------
-- STEP 8: What PostgreSQL does NOT enforce (this one SUCCEEDS)
------------------------------------------------------------------

-- Note pointing at event 9999, which does not exist
INSERT INTO notes (body, notable_type, notable_id, created_at, updated_at)
VALUES ('Orphan note: this event does not exist', 'event', 9999, NOW(), NOW());

-- Show it was accepted
SELECT n.id, n.body, n.notable_type, n.notable_id,
       (SELECT COUNT(*) FROM events e WHERE e.id = n.notable_id) AS matching_events
FROM notes n
WHERE n.notable_id = 9999;

------------------------------------------------------------------
-- STEP 9: Cleanup (ON DELETE CASCADE)
------------------------------------------------------------------

-- Before: event 6 (Foundation Day Concert) has 3 registrations
SELECT * FROM event_participant WHERE event_id = 6;

-- Delete the event
DELETE FROM events WHERE id = 6;

-- After: its registrations are gone (0 rows)
SELECT * FROM event_participant WHERE event_id = 6;
