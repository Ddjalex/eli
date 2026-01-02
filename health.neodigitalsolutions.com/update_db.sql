-- SQL for manually adding the meal_plan_id column to the payments table in your MySQL database
ALTER TABLE payments ADD COLUMN meal_plan_id INT NULL;
