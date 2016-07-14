SELECT p.post_title, 
       p.post_date,
	   COUNT (p.post_title),
       pm.*
FROM nwp_posts p 
INNER JOIN nwp_postmeta pm 
ON p.ID = pm.post_id
WHERE p.post_type = 'reviews'
AND p.post_status = 'publish'
AND pm.meta_key = 'rating'
GROUP BY meta_value

SELECT 
	   COUNT(DISTINCT(p.ID)) as `count`,
       pm.meta_value as `rating`
FROM nwp_posts p 
INNER JOIN nwp_postmeta pm 
ON p.ID = pm.post_id
WHERE p.post_type = 'reviews'
AND p.post_status = 'publish'
AND pm.meta_key = 'rating'
GROUP BY meta_value
ORDER BY meta_value DESC

SELECT p.post_title, 
       p.post_date,
       pm.*
FROM nwp_posts p 
INNER JOIN nwp_postmeta pm 
ON p.ID = pm.post_id
WHERE p.post_type = 'reviews'
AND p.post_status = 'publish'