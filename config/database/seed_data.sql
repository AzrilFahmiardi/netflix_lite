-- StreamFlix Database Seeder
-- Sample data for development and testing

USE streamflix;

-- Insert Genres
INSERT INTO genres (name, description) VALUES 
('Action', 'High-energy films with exciting sequences'),
('Sci-Fi', 'Science fiction and futuristic themes'),
('Romance', 'Love stories and romantic themes'),
('Thriller', 'Suspenseful and tension-filled stories'),
('Comedy', 'Humorous and entertaining content'),
('Horror', 'Scary and supernatural themes'),
('Drama', 'Serious and emotional storytelling'),
('Adventure', 'Exciting journeys and explorations'),
('Fantasy', 'Magical and mythical worlds'),
('Crime', 'Criminal activities and investigations');

-- Insert Cast & Crew
INSERT INTO cast_crew (name, role, nationality, bio, birth_date) VALUES 
('Emma Stone', 'actor', 'American', 'Academy Award-winning actress', '1988-11-06'),
('Ryan Gosling', 'actor', 'Canadian', 'Canadian actor known for versatile roles', '1980-11-12'),
('Zendaya', 'actor', 'American', 'Multi-talented actress and singer', '1996-09-01'),
('Christopher Nolan', 'director', 'British', 'Acclaimed director known for complex narratives', '1970-07-30'),
('Hans Zimmer', 'writer', 'German', 'Legendary film composer', '1957-09-12'),
('Gal Gadot', 'actor', 'Israeli', 'Actress known for action roles', '1985-04-30'),
('Tom Hardy', 'actor', 'British', 'Versatile British actor', '1977-09-15'),
('Margot Robbie', 'actor', 'Australian', 'Australian actress and producer', '1990-07-02'),
('Robert Downey Jr.', 'actor', 'American', 'Famous for Iron Man role', '1965-04-04'),
('Scarlett Johansson', 'actor', 'American', 'Action and drama actress', '1984-11-22');

-- Insert 20 Movies
INSERT INTO movies (title, description, director, release_year, duration_minutes, genre, rating, trailer_youtube_id, movie_youtube_id, is_featured, view_count) VALUES 
('The Quantum Realm', 'A mind-bending sci-fi adventure through dimensions', 'Christopher Nolan', 2023, 127, 'Sci-Fi', 9.1, 'zSWdZVtXT7E', 'zSWdZVtXT7E', TRUE, 15234),
('Interstellar', 'A team of explorers travel through a wormhole in space', 'Christopher Nolan', 2014, 169, 'Sci-Fi', 8.6, '2QKg5SZ_35I', '2QKg5SZ_35I', TRUE, 23445),
('Inception', 'A thief who enters peoples dreams', 'Christopher Nolan', 2010, 148, 'Sci-Fi', 8.8, 'nyc6RJEEe0U', 'nyc6RJEEe0U', TRUE, 31267),
('Doctor Strange', 'A surgeon discovers the world of magic', 'Scott Derrickson', 2016, 115, 'Action', 7.5, 'LY7x2Ihqjmc', 'LY7x2Ihqjmc', FALSE, 18921),
('Avengers: Endgame', 'The final battle against Thanos', 'Russo Brothers', 2019, 181, 'Action', 8.4, 'TcMBFSGVi1c', 'TcMBFSGVi1c', TRUE, 42156),
('Spider-Man: No Way Home', 'Spider-Man faces multiverse chaos', 'Jon Watts', 2021, 148, 'Action', 8.2, 'JfVOs4VSpmA', 'JfVOs4VSpmA', FALSE, 38945),
('The Dark Knight', 'Batman faces the Joker', 'Christopher Nolan', 2008, 152, 'Action', 9.0, 'EXeTwQWrcwY', 'EXeTwQWrcwY', TRUE, 51234),
('Iron Man', 'Tony Stark becomes Iron Man', 'Jon Favreau', 2008, 126, 'Action', 7.9, '8ugaeA-nMTc', '8ugaeA-nMTc', FALSE, 29876),
('Wonder Woman', 'Diana Prince discovers her powers', 'Patty Jenkins', 2017, 141, 'Action', 7.4, '1Q8fG0TtVAY', '1Q8fG0TtVAY', FALSE, 22134),
('Black Widow', 'Natasha Romanoff confronts her past', 'Cate Shortland', 2021, 134, 'Action', 6.7, 'Fp9pNPdNwjI', 'Fp9pNPdNwjI', FALSE, 16789),
('Guardians of the Galaxy', 'A group of misfits save the galaxy', 'James Gunn', 2014, 121, 'Action', 8.0, 'd96cjJhvlMA', 'd96cjJhvlMA', FALSE, 33421),
('The Matrix', 'Neo discovers the truth about reality', 'Wachowski Sisters', 1999, 136, 'Sci-Fi', 8.7, 'vKQi3bBA1y8', 'vKQi3bBA1y8', TRUE, 45678),
('Blade Runner 2049', 'A young blade runner discovers secrets', 'Denis Villeneuve', 2017, 164, 'Sci-Fi', 8.0, 'gCcx85zbxz4', 'gCcx85zbxz4', FALSE, 19234),
('Dune', 'Paul Atreides journey on planet Arrakis', 'Denis Villeneuve', 2021, 155, 'Sci-Fi', 8.0, '8g18jFHCLXk', '8g18jFHCLXk', TRUE, 27543),
('Mad Max: Fury Road', 'Post-apocalyptic action adventure', 'George Miller', 2015, 120, 'Action', 8.1, 'hEJnMQG9ev8', 'hEJnMQG9ev8', FALSE, 21876),
('John Wick', 'An ex-hitman seeks vengeance', 'Chad Stahelski', 2014, 101, 'Action', 7.4, 'C0BMx-qxsP4', 'C0BMx-qxsP4', FALSE, 18765),
('The Joker', 'Origin story of Gothams famous villain', 'Todd Phillips', 2019, 122, 'Drama', 8.4, 'zAGVQLHvwOY', 'zAGVQLHvwOY', FALSE, 35421),
('Parasite', 'A poor family infiltrates a rich household', 'Bong Joon-ho', 2019, 132, 'Thriller', 8.6, '5xH0HfJHsaY', '5xH0HfJHsaY', FALSE, 24567),
('La La Land', 'A jazz musician and actress fall in love', 'Damien Chazelle', 2016, 128, 'Romance', 8.0, '0pdqf4P9MB8', '0pdqf4P9MB8', FALSE, 19876),
('Top Gun: Maverick', 'Maverick returns to train new pilots', 'Joseph Kosinski', 2022, 131, 'Action', 8.3, 'qSqVVswa420', 'qSqVVswa420', TRUE, 41234);

-- Link Movies with Genres (Many-to-Many relationship)
INSERT INTO movie_genres (movie_id, genre_id) VALUES 
(1, 2), (1, 8),  -- The Quantum Realm: Sci-Fi, Adventure
(2, 2), (2, 7),  -- Interstellar: Sci-Fi, Drama
(3, 2), (3, 4),  -- Inception: Sci-Fi, Thriller
(4, 1), (4, 9),  -- Doctor Strange: Action, Fantasy
(5, 1), (5, 8),  -- Avengers Endgame: Action, Adventure
(6, 1), (6, 2),  -- Spider-Man: Action, Sci-Fi
(7, 1), (7, 10), -- The Dark Knight: Action, Crime
(8, 1), (8, 2),  -- Iron Man: Action, Sci-Fi
(9, 1), (9, 9),  -- Wonder Woman: Action, Fantasy
(10, 1), (10, 4), -- Black Widow: Action, Thriller
(11, 1), (11, 5), -- Guardians: Action, Comedy
(12, 2), (12, 1), -- The Matrix: Sci-Fi, Action
(13, 2), (13, 7), -- Blade Runner: Sci-Fi, Drama
(14, 2), (14, 8), -- Dune: Sci-Fi, Adventure
(15, 1), (15, 8), -- Mad Max: Action, Adventure
(16, 1), (16, 4), -- John Wick: Action, Thriller
(17, 7), (17, 4), -- Joker: Drama, Thriller
(18, 4), (18, 7), -- Parasite: Thriller, Drama
(19, 3), (19, 7), -- La La Land: Romance, Drama
(20, 1), (20, 7); -- Top Gun: Action, Drama

-- Link Movies with Cast & Crew
INSERT INTO movie_cast_crew (movie_id, person_id, role_in_movie, character_name) VALUES 
(1, 1, 'Lead Actor', 'Dr. Sarah Chen'),
(1, 4, 'Director', NULL),
(1, 5, 'Music Composer', NULL),
(3, 4, 'Director', NULL),
(4, 6, 'Lead Actor', 'Stephen Strange'),
(5, 9, 'Lead Actor', 'Tony Stark'),
(5, 10, 'Supporting Actor', 'Natasha Romanoff'),
(9, 6, 'Lead Actor', 'Diana Prince'),
(10, 10, 'Lead Actor', 'Natasha Romanoff');

-- Insert Sample Users
INSERT INTO users (username, email, password, first_name, last_name, phone, country) VALUES 
('johndoe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '+1234567890', 'United States'),
('jansmith', 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', '+1234567891', 'Canada'),
('mikejohnson', 'mike.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Johnson', '+1234567892', 'United Kingdom'),
('sarahwilson', 'sarah.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Wilson', '+1234567893', 'Australia'),
('davidlee', 'david.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Lee', '+1234567894', 'Indonesia');

-- Insert Sample Reviews
INSERT INTO user_reviews (user_id, movie_id, rating, review_text) VALUES 
(1, 1, 5, 'Amazing movie! The quantum physics concepts were mind-blowing.'),
(2, 1, 4, 'Great visual effects and storyline, but a bit confusing at times.'),
(1, 3, 5, 'Christopher Nolan is a genius! Inception is a masterpiece.'),
(3, 3, 4, 'Complex plot but worth watching multiple times.'),
(2, 5, 5, 'Perfect ending to the Marvel saga. Emotional and epic!'),
(4, 5, 5, 'Best superhero movie ever made. The final battle was incredible.'),
(3, 7, 5, 'Heath Ledgers Joker performance is unforgettable.'),
(5, 12, 4, 'Revolutionary movie that changed sci-fi forever.'),
(1, 14, 4, 'Visually stunning adaptation of the classic novel.'),
(4, 19, 5, 'Beautiful love story with amazing music and cinematography.');

-- Insert Sample Watchlist
INSERT INTO watchlist (user_id, movie_id) VALUES 
(1, 2), (1, 4), (1, 6),
(2, 3), (2, 7), (2, 8),
(3, 1), (3, 5), (3, 9),
(4, 10), (4, 11), (4, 12),
(5, 13), (5, 14), (5, 15);

-- Insert Admin User
INSERT INTO admin_users (username, email, password, role) VALUES 
('admin', 'admin@streamflix.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');
