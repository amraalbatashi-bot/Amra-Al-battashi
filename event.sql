CREATE DATABASE IF NOT EXISTS db;
USE db;

-- Users
CREATE TABLE IF NOT EXISTS USERt (
    User_ID VARCHAR(15) PRIMARY KEY,
    Fname VARCHAR(10) NOT NULL,
    Email VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Phone INT UNIQUE,
    CHECK (Phone REGEXP '^[97][0-9]{7}$')
);

-- Admins
CREATE TABLE IF NOT EXISTS ADMINt (
    Admin_ID VARCHAR(15) PRIMARY KEY,
    Admin_Name VARCHAR(10) NOT NULL,
    Email VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Phone INT UNIQUE
);

-- Events
CREATE TABLE IF NOT EXISTS EVENT (
    Event_ID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(30) NOT NULL,
    Description VARCHAR(150) NOT NULL,
    Event_Type VARCHAR(15) NOT NULL,
    Location VARCHAR(30) NOT NULL,
    DateE DATE NOT NULL,
    TimeE TIME NOT NULL,
    End_TimeE TIME NOT NULL,
	Resources VARCHAR(255) DEFAULT '',
	 Assigned_Resource VARCHAR(50) DEFAULT NULL  -- This column stores resource name
);

-- Bookings (linked to user and event)
CREATE TABLE IF NOT EXISTS BOOKING (
    Booking_ID INT AUTO_INCREMENT PRIMARY KEY,
    User_ID VARCHAR(15) NOT NULL,
    Event_ID INT NOT NULL,
    Booking_Date DATE NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES USERt(User_ID) ON DELETE CASCADE,
    FOREIGN KEY (Event_ID) REFERENCES EVENT(Event_ID) ON DELETE CASCADE
);

-- Resources
CREATE TABLE IF NOT EXISTS RESOURCES (
    Resource_ID INT AUTO_INCREMENT PRIMARY KEY,
    Resource_Name VARCHAR(15) NOT NULL,
	Event_ID INT NOT NULL,
	FOREIGN KEY (Event_ID) REFERENCES EVENT(Event_ID) ON DELETE CASCADE
);

-- Feedback
CREATE TABLE FEEDBACK (
    Feedback_ID INT AUTO_INCREMENT PRIMARY KEY,
    User_ID VARCHAR(15) NOT NULL,
    Event_ID INT NOT NULL,
    Comments VARCHAR(50) NOT NULL,
    Rating INT NOT NULL,
    Date_Submitted DATE NOT NULL ,

    FOREIGN KEY (User_ID) REFERENCES USERt(User_ID) ON DELETE CASCADE,
    FOREIGN KEY (Event_ID) REFERENCES EVENT(Event_ID) ON DELETE CASCADE
);





-- Example Event
INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Drone Fly', 'Practical Applications of Drones in Various Aspects of Life', 'Learning', 'MPH - Multipurpose Room', '2025-11-19', '09:00:00', '10:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Youth Verse', '✨ Ready to create your first game? ✨
Join us at YouthVerse in celebration of Omani Youth Day!
Turn your ideas into reality in an exciting Game Development Hackathon, powered by Oman Arab Bank. 🚀🎮

', 'Hackathon', 'Oman Arab Bank Hall', '2025-10-25', '08:00:00', '09:00:00');


INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Hiking & Wadi Clean-Up Day', 'Join the CECS for a Hiking & Wadi Clean-Up at Wadi Al Khod! 🌿✨
Explore nature, protect it, and enjoy the outdoors together.', 'Hiking', 'Wadi Al Khod', '2025-10-28', '04:00:00', '08:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('AI Awareness Workshop', 'An introductory workshop on AI tools and real-world applications', 'Workshop', 'IT Building Lab 3', '2025-11-10', '10:00:00', '12:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Career Fair 2025', 'Meet top companies and explore internship & job opportunities', 'Career', 'Main Hall', '2025-12-02', '09:00:00', '14:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Robotics Bootcamp', 'Hands-on robotics training focusing on Arduino and automation', 'Training', 'Engineering Workshop', '2025-11-15', '13:00:00', '17:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Photography Walk', 'Learn mobile photography techniques during a guided campus walk', 'Activity', 'Campus Garden', '2025-10-30', '16:00:00', '18:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Mental Health Awareness Day', 'Interactive sessions and activities for stress management and well-being', 'Awareness', 'Auditorium', '2025-10-22','11:00:00','13:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Coding Challenge Night', 'Competitive coding event with prizes for the top performers', 'Competition', 'Lab 5', '2025-11-25', '18:00:00', '21:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Green Campus Initiative', 'Tree planting activity and sustainability awareness session', 'Community Service', 'Block C Garden Area', '2025-12-05', '08:30:00', '11:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Data Science Crash Course', 'A fast-paced introduction to data analysis, visualization, and machine learning basics', 'Workshop', 'Lab 2', '2025-12-10', '09:00:00', '12:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Sports Day 2025', 'A full day of friendly competitions including football, volleyball, and sprint races', 'Sports', 'Sports Ground', '2025-11-30', '08:00:00', '15:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Cultural Night', 'An evening celebrating diverse cultures through music, food, and performances', 'Cultural', 'Main Auditorium', '2025-12-18', '17:00:00', '20:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Cybersecurity Awareness Talk', 'Guidelines on safe browsing, password security, and recognizing scams', 'Awareness', 'Lecture Theatre 1', '2025-10-27', '10:30:00', '11:30:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Resume Building Workshop', 'Hands-on session on creating a professional CV and LinkedIn profile', 'Training', 'MPH - Multipurpose Room', '2025-11-02', '13:00:00', '15:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Innovation Expo', 'Showcase of student projects and prototypes in engineering and computing', 'Exhibition', 'Expo Hall', '2025-12-12', '09:00:00', '14:00:00');

INSERT INTO EVENT (Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE) VALUES
('Arabic Calligraphy Workshop', 'A creative workshop introducing modern and traditional Arabic calligraphy styles', 'Art', 'Room C104', '2025-11-22', '14:00:00', '16:00:00');



INSERT INTO ADMINt (Admin_ID, Admin_Name, Email, Password, Phone) 
VALUES ('16j6666', 'Ali', '16j6666@utas.edu.om', 'Aa12345', 99999999);



INSERT INTO RESOURCES (Event_ID, Resource_Name) VALUES
(1, 'Projector'),
(1, 'Batteries'),
(1, 'Chairs'),
(1, 'Tables');



INSERT INTO RESOURCES (Event_ID, Resource_Name) VALUES
(2, 'Projector'),
(3, 'Batteries'),
(6, 'Chairs'),
(9, 'Tables');