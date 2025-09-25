DROP PROCEDURE IF EXISTS sp_monthly_user_aggregation;
DELIMITER $$
CREATE PROCEDURE sp_monthly_user_aggregation(IN start_date DATE, IN end_date DATE)
BEGIN
    SELECT user_id, DATE_FORMAT(created_at, '%Y-%m-01') AS month, COUNT(*) AS tx_count, SUM(amount) AS total_amount
    FROM transactions
    WHERE created_at BETWEEN start_date AND end_date
    GROUP BY user_id, month;
END$$
DELIMITER ;
