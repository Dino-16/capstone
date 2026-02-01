-- Fix Existing Misaligned Qualification Status Records
-- This SQL script recalculates qualification_status based on rating_score for ALL existing records

-- Update all records to have the correct qualification status based on their score
UPDATE filtered_resumes
SET qualification_status = CASE
    WHEN rating_score >= 90 THEN 'Exceptional'
    WHEN rating_score >= 80 THEN 'Highly Qualified'
    WHEN rating_score >= 70 THEN 'Qualified'
    WHEN rating_score >= 60 THEN 'Moderately Qualified'
    WHEN rating_score >= 50 THEN 'Marginally Qualified'
    ELSE 'Not Qualified'
END
WHERE rating_score IS NOT NULL;

-- Show the results after update
SELECT 
    id,
    application_id,
    rating_score,
    qualification_status,
    updated_at
FROM filtered_resumes
ORDER BY rating_score DESC;
