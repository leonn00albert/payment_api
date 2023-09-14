-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 14, 2023 at 04:25 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bmdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `uid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `released` varchar(255) DEFAULT NULL,
  `runtime` varchar(255) DEFAULT NULL,
  `genre` varchar(255) DEFAULT NULL,
  `director` varchar(255) DEFAULT NULL,
  `actors` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `imdb` decimal(3,1) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `overview` text DEFAULT NULL,
  `imdb_id` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `uid`, `title`, `year`, `released`, `runtime`, `genre`, `director`, `actors`, `country`, `poster`, `imdb`, `type`, `created_at`, `updated_at`, `overview`, `imdb_id`) VALUES
(4, '793002', 'Dreamcatcher', '2021', '2021-03-05', '90 min', '', 'Jacob Johnston', 'Niki Koss, Zachary Gordon, Travis Burns, Blaine Kern III, Adrienne Wilkinson', 'United States of America', '/3CF743g2BpC8r3mCVV9gSX2yQBj.jpg', 5.9, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'The film centers on two estranged sisters who, along with their friends, become entrenched in a 48 hour whirlwind of violence after a traumatic experience at an underground music festival.', 'tt9382172'),
(5, '24548', 'Just One of the Guys', '1985', '1985-04-26', '100 min', '', 'Lisa Gottlieb', 'Joyce Hyser, Clayton Rohner, Billy Jayne, Toni Hudson, William Zabka', 'United States of America', '/7ffvsRv8ueYYK0H3zocmNRPd8yN.jpg', 6.5, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'When Terry Griffith loses her high school\'s writing competition, she\'s convinced that it\'s because she\'s a girl. So Terry decides to change high schools and pose as a boy to prove her point. Her brother, Buddy, helps her pass as a guy so well that she is soon making friends with the boys at school, including the attractive Rick, who becomes her new best friend. But her gender-swapping makes things difficult when she falls in love with him.', 'tt0089393'),
(6, '11962', 'Joe\'s Apartment', '1996', '1996-07-26', '78 min', '', 'John Payson', 'Jerry O\'Connell, Megan Ward, Billy West, Reginald Hudlin, Willi One Blood', 'United States of America', '/7OBbURUtU37ucxHGUVkD598f6t3.jpg', 5.5, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'A nice guy has just moved to New York and discovers that he must share his run-down apartment with a couple thousand singing, dancing cockroaches.', 'tt0116707'),
(7, '9058', 'Only You', '1994', '1994-03-04', '115 min', '', 'Norman Jewison', 'Marisa Tomei, Robert Downey Jr., Bonnie Hunt, Joaquim de Almeida, Fisher Stevens', 'United States of America', '/7mLLANXgUg3bibTiUitDlB0NZcU.jpg', 6.8, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'A childhood incident has convinced Faith Corvatch that her true love is a guy named \"Damon Bradley,\" but she has yet to meet him. Preparing to settle down and marry a foot doctor, Faith impulsively flies to Venice when it seems that she may be able to finally encounter the man of her dreams. Instead, she meets the charming Peter Wright. But can they fall in love if she still believes that she is intended to be with someone else?', 'tt0110737'),
(8, '4727', 'Le Gendarme de Saint-Tropez', '1964', '1964-09-08', '89 min', '', 'Jean Girault', 'Louis de Funès, Geneviève Grad, Michel Galabru, Daniel Cauchy, Maria Pacôme', 'France', '/6510K6Y8eYLf9FJ7O4xaOnwhn2u.jpg', 7.1, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'The ambitious police officer Cruchot is transferred to St. Tropez. He\'s struggling with crimes such as persistent nude swimming, but even more with his teenage daughter, who\'s trying to impress her rich friends by telling them her father was a millionaire and owned a yacht in the harbor.', 'tt0058135'),
(9, '294652', 'Son of a Gun', '2014', '2014-10-16', '108 min', '', 'Julius Avery', 'Ewan McGregor, Brenton Thwaites, Alicia Vikander, Jacek Koman, Matt Nable', 'Australia', '/yRLVyhQfkW2hn15e8NIqarswLAr.jpg', 6.4, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'Locked up for a minor crime, 19 year old JR quickly learns the harsh realities of prison life. Protection, if you can get it, is paramount. JR soon finds himself under the watchful eye of Australia\'s most notorious criminal, Brendan Lynch, but protection comes at a price.', 'tt2452200'),
(10, '15916', 'Angel\'s Egg', '1985', '1985-12-22', '71 min', '', 'Mamoru Oshii', 'Keiichi Noda, Mako Hyoudou, Jinpachi Nezu', 'Japan', '/dcEUGvckbePFzPKhGXnS9T3kZMG.jpg', 7.7, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'In a desolate and dark world full of shadows, lives one little girl who seems to do nothing but collect water in jars and protect a large egg she carries everywhere. A mysterious man enters her life... and they discuss the world around them.', 'tt0208502'),
(11, '1727', 'Bird on a Wire', '1990', '1990-05-18', '110 min', '', 'John Badham', 'Mel Gibson, Goldie Hawn, David Carradine, Bill Duke, Stephen Tobolowsky', 'United States of America', '/vhsv2PrAPZ79ZpV64AJ2TiEI2zX.jpg', 6.2, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'An FBI informant has kept his new identity secret for 15 years. Now an old flame has recognised him, and the bad guys are back for revenge.', 'tt0099141'),
(12, '1061605', 'Don\'t Look Deeper', '2023', '2023-09-07', '119 min', '', 'Catherine Hardwicke', 'Helena Howard, Ema Horvath, Jan Luis Castellanos, Harvey Zielinski, Kaiwi Lyman', 'United States of America', '/pPlBfssnI49kMKrHUY1qbVnHpnO.jpg', 0.0, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'A high school student in central California sets off an unexpected series of events when she begins to doubt if she\'s human.', 'tt22811298'),
(13, '606870', 'The Survivor', '2022', '2022-04-07', '129 min', '', 'Barry Levinson', 'Ben Foster, Billy Magnussen, Vicky Krieps, Peter Sarsgaard, Saro Emirze', 'Canada', '/oZWJ20tGWZ5xO9CrTCVavmDRy7J.jpg', 7.2, 'movie', '2023-09-04 14:42:55', '0000-00-00 00:00:00', 'Harry Haft is a boxer who fought fellow prisoners in the concentration camps to survive. Haunted by the memories and his guilt, he attempts to use high-profile fights against boxing legends like Rocky Marciano as a way to find his first love again.', 'tt9242528'),
(14, '10912', 'All Quiet on the Western Front', '1979', '1979-11-14', '150 min', '', 'Delbert Mann', 'Richard Thomas, Ernest Borgnine, Donald Pleasence, Ian Holm, Patricia Neal', 'United Kingdom', '/2HHFfieloWJcIz5PU3NKrv1i6DJ.jpg', 6.6, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'At the start of World War I, Paul Baumer is a young German patriot, eager to fight. Indoctrinated with propaganda at school, he and his friends eagerly sign up for the army soon after graduation. But when the horrors of war soon become too much to bear, and as his friends die or become gravely wounded, Paul questions the sanity of fighting over a few hundreds yards of war-torn countryside.', 'tt0078753'),
(15, '146239', 'Delivery Man', '2013', '2013-10-10', '105 min', '', 'Ken Scott', 'Vince Vaughn, Cobie Smulders, Chris Pratt, Britt Robertson, Jack Reynor', 'United States of America', '/dKTkxSeNgHdwgrAbhwbXuUk4tzb.jpg', 6.2, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'An affable underachiever finds out he\'s fathered 533 children through anonymous donations to a fertility clinic 20 years ago. Now he must decide whether or not to come forward when 142 of them file a lawsuit to reveal his identity.', 'tt2387559'),
(16, '54523', 'Hitch Hike', '1977', '1977-04-30', '104 min', '', 'Pasquale Festa Campanile', 'Franco Nero, Corinne Cléry, David Hess, Joshua Sinclair, Ignazio Spalla', 'Italy', '/qo8Z0japEz6xKR9OBPslzKKqmND.jpg', 6.9, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'A bickering couple driving cross-country pick up a murderous hitchhiker who threatens to kill them unless they take him to a sanctuary. In return he agrees to split some bank loot he has on him.', 'tt0077188'),
(17, '17894', 'The Beautician and the Beast', '1997', '1997-02-07', '105 min', '', 'Ken Kwapis', 'Timothy Dalton, Fran Drescher, Michael Lerner, Ian McNeice, Patrick Malahide', 'United States of America', '/s2RaTrqoPvD4eYEaZCyF0zFbbpV.jpg', 6.8, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'A New York City beautician is mistakenly hired as the school teacher for the children of the president of a small Eastern European country.', 'tt0118691'),
(18, '261103', 'Maya the Bee Movie', '2014', '2014-09-11', '79 min', '', 'Alexs Stadermann', 'Coco Jack Gillies, Kodi Smit-McPhee, Richard Roxburgh, Noah Taylor, Justine Clarke', 'Germany', '/pMQ88CvnQroSjxk4IhM7YNbcjTx.jpg', 6.2, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'Freshly hatched bee Maya is a little whirlwind and won\'t follow the rules of the hive. One of these rules is not to trust the hornets that live beyond the meadow. When the Royal Jelly is stolen, the hornets are suspected and Maya is thought to be their accomplice. No one believes that she is the innocent victim and no one will stand by her except for her good-natured and best friend Willy. After a long and eventful journey to the hornets hive Maya and Willy soon discover the true culprit and the two friends finally bond with the other residents of the opulent meadow.', 'tt3336368'),
(19, '973530', 'Hidden', '2022', '2022-05-25', '0 min', '', 'Han Jonghun', 'Jung Hye-in, Gong Hyung-jin, Kim In-kwon, Jo Ha-seok, Shim So-young', NULL, '/lgkynMLnO0Ihzu08nTlOiMPNSFJ.jpg', 6.0, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'The seawater belonging to the National Intelligence Service\'s International Crime (Drug) Team is called \'Panfunction\' in the house on behalf of a colleague who died during the operation, and the blankjack of the unidentified gambling, \'Blackjack\', aims for 6 billion won drugs. You know that there is. Now there is only one way to catch him. Seawater decides to jump directly into dangerous gambling boards.', NULL),
(20, '87093', 'Big Eyes', '2014', '2014-12-24', '106 min', '', 'Tim Burton', 'Amy Adams, Christoph Waltz, Danny Huston, Jon Polito, Krysten Ritter', 'Canada', '/203HAjJcLMl7xThcTqZx4zmEGcV.jpg', 7.0, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'In the late 1950s and early \'60s, artist Walter Keane achieves unbelievable fame and success with portraits of saucer-eyed waifs. However, no one realizes that his wife, Margaret, is the real painter behind the brush. Although Margaret is horrified to learn that Walter is passing off her work as his own, she is too meek to protest too loudly. It isn\'t until the Keanes\' marriage comes to an end and a lawsuit follows that the truth finally comes to light.', 'tt1126590'),
(21, '10854', 'Wicked Little Things', '2006', '2006-11-17', '94 min', '', 'J.S. Cardone', 'Lori Heuring, Scout Taylor-Compton, Chloë Grace Moretz, Geoffrey Lewis, Ben Cross', 'United States of America', '/wXymWZpjkeHUIi0G2YZYdcO1OH0.jpg', 5.7, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'Karen, Sarah, and Emma Tunney are all moving to a small town in Pennsylvania where, unknown to them, in 1913, a horrid mine accident trapped dozens of children alive, underground. But there\'s a problem. They\'re still alive.', 'tt0470000'),
(22, '9667', 'The Jacket', '2005', '2005-03-04', '103 min', '', 'John Maybury', 'Adrien Brody, Keira Knightley, Kris Kristofferson, Jennifer Jason Leigh, Kelly Lynch', 'Germany', '/bt89UfRhjAvJDAO9CWJnRFXAN3p.jpg', 6.9, 'movie', '2023-09-04 14:45:00', '0000-00-00 00:00:00', 'A military veteran goes on a journey into the future, where he can foresee his death and is left with questions that could save his life and those he loves.', 'tt0366627'),
(23, '424277', 'Annette', '2021', '2021-07-06', '140 min', '', 'Leos Carax', 'Adam Driver, Marion Cotillard, Simon Helberg, Devyn McDowell, Ron Mael', 'Belgium', '/4FTnypxpGltJdIARrfFsP31pGTp.jpg', 6.8, 'movie', '2023-09-04 14:45:01', '0000-00-00 00:00:00', 'The story of Henry, a stand-up comedian with a fierce sense of humour and Ann, a singer of international renown. In the spotlight, they are the perfect couple, healthy, happy, and glamourous. The birth of their first child, Annette, a mysterious girl with an exceptional destiny, will change their lives.', 'tt6217926'),
(24, '157849', 'A Most Wanted Man', '2014', '2014-07-25', '121 min', '', 'Anton Corbijn', 'Philip Seymour Hoffman, Willem Dafoe, Robin Wright, Rachel McAdams, Grigoriy Dobrygin', 'United Kingdom', '/6B76Z5Ct758RfKFoFg37skVRiMp.jpg', 6.5, 'movie', '2023-09-04 14:45:01', '0000-00-00 00:00:00', 'A Chechen Muslim illegally immigrates to Hamburg and becomes a person of interest for a covert government team which tracks the movements of potential terrorists.', 'tt1972571'),
(25, '72710', 'The Host', '2013', '2013-03-22', '125 min', '', 'Andrew Niccol', 'Saoirse Ronan, Diane Kruger, Max Irons, Jake Abel, William Hurt', 'United States of America', '/ok2sl6rGITZ0W94DeRU4VkB2ssW.jpg', 6.3, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'A parasitic alien soul is injected into the body of Melanie Stryder. Instead of carrying out her race\'s mission of taking over the Earth, \"Wanda\" (as she comes to be called) forms a bond with her host and sets out to aid other free humans.', 'tt1517260'),
(26, '293', 'A River Runs Through It', '1992', '1992-10-09', '123 min', '', 'Robert Redford', 'Craig Sheffer, Brad Pitt, Tom Skerritt, Brenda Blethyn, Edie McClurg', 'United States of America', '/rL4odYIaO0xcksUQ9qzRFfU6lH2.jpg', 7.0, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'A River Runs Through It is a cinematographically stunning true story of Norman Maclean. The story follows Norman and his brother Paul through the experiences of life and growing up, and how their love of fly fishing keeps them together despite varying life circumstances in the untamed west of Montana in the 1920s.', 'tt0105265'),
(27, '816', 'Austin Powers: International Man of Mystery', '1997', '1997-05-02', '94 min', '', 'Jay Roach', 'Mike Myers, Elizabeth Hurley, Michael York, Mimi Rogers, Robert Wagner', 'Germany', '/5uD4dxNX8JKFjWKYMHyOsqhi5pN.jpg', 6.6, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'As a swingin\' fashion photographer by day and a groovy British superagent by night, Austin Powers is the \'60s\' most shagadelic spy, baby! But can he stop megalomaniac Dr. Evil after the bald villain freezes himself and unthaws in the \'90s? With the help of sexy sidekick Vanessa Kensington, he just might.', 'tt0118655'),
(28, '10012', 'Cursed', '2005', '2005-02-25', '97 min', '', 'Wes Craven', 'Christina Ricci, Jesse Eisenberg, Joshua Jackson, Judy Greer, Scott Baio', 'Germany', '/em45jL4CfTMyj5V83kj7rhdorJu.jpg', 5.4, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'In Los Angeles, siblings Ellie and Jimmy come across an accident on Mulholland Drive. As they try to help the woman caught in the wreckage, a ferocious creature attacks them, devouring the woman and scratching the terrified siblings. They slowly discover that the creature was a werewolf and that they have fallen victim to a deadly curse.', 'tt0257516'),
(29, '21385', 'Mickey, Donald, Goofy: The Three Musketeers', '2004', '2004-08-04', '68 min', '', 'Donovan Cook', 'Wayne Allwine, Tony Anselmo, Bill Farmer, Russi Taylor, Tress MacNeille', 'United States of America', '/23bvOwfOS9fw347Yc68yPpkmd8i.jpg', 6.7, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'In Disney\'s take on the Alexander Dumas tale, Mickey Mouse, Donald Duck and Goofy want nothing more than to perform brave deeds on behalf of their queen (Minnie Mouse), but they\'re stymied by the head Musketeer, Pete. Pete secretly wants to get rid of the queen, so he appoints Mickey and his bumbling friends as guardians to Minnie, thinking such a maneuver will ensure his scheme\'s success. The score features songs based on familiar classical melodies.', 'tt0371823'),
(30, '3049', 'Ace Ventura: Pet Detective', '1994', '1994-02-04', '86 min', '', 'Tom Shadyac', 'Jim Carrey, Courteney Cox, Sean Young, Tone Loc, Dan Marino', 'United States of America', '/pqiRuETmuSybfnVZ7qyeoXhQyN1.jpg', 6.5, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'He\'s Ace Ventura: Pet Detective. Jim Carrey is on the case to find the Miami Dolphins\' missing mascot and quarterback Dan Marino. He goes eyeball to eyeball with a man-eating shark, stakes out the Miami Dolphins and woos and wows the ladies. Whether he\'s undercover, under fire or underwater, he always gets his man… or beast!', 'tt0109040'),
(31, '258230', 'A Monster Calls', '2016', '2016-10-07', '108 min', '', 'J. A. Bayona', 'Lewis MacDougall, Sigourney Weaver, Felicity Jones, Toby Kebbell, Ben Moor', 'Spain', '/eNTalDnE6AcFKghdvws2ckguYWC.jpg', 7.3, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'A boy imagines a monster that helps him deal with his difficult life and see the world in a different way.', 'tt3416532'),
(32, '437557', 'Blockers', '2018', '2018-03-14', '102 min', '', 'Kay Cannon', 'Leslie Mann, John Cena, Ike Barinholtz, Kathryn Newton, Geraldine Viswanathan', 'China', '/uvlUQXg0AlpGzKukO11K7QtW3Yu.jpg', 6.3, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'When three parents discover that each of their daughters have a pact to lose their virginity at prom, they launch a covert one-night operation to stop the teens from sealing the deal.', 'tt2531344'),
(33, '10957', 'The Black Cauldron', '1985', '1985-07-24', '80 min', '', 'Ted Berman', 'Grant Bardsley, Susan Sheridan, John Byner, Nigel Hawthorne, John Hurt', 'United States of America', '/act8vtlXVEizdsUf9FcKbzSERew.jpg', 6.4, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'Taran is an assistant pigkeeper with boyish dreams of becoming a great warrior. However, he has to put the daydreaming aside when his charge, an oracular pig named Hen Wen, is kidnapped by an evil lord known as the Horned King. The villain hopes Hen will show him the way to The Black Cauldron, which has the power to create a giant army of unstoppable soldiers.', 'tt0088814'),
(34, '9879', 'Striptease', '1996', '1996-06-28', '115 min', '', 'Andrew Bergman', 'Demi Moore, Burt Reynolds, Armand Assante, Ving Rhames, Robert Patrick', 'United States of America', '/edkpT3vgjEjVrF461AbIwZgkvC7.jpg', 5.5, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'Bounced from her job, Erin Grant needs money if she\'s to have any chance of winning back custody of her child. But, eventually, she must confront the naked truth: to take on the system, she\'ll have to take it all off. Erin strips to conquer, but she faces unintended circumstances when a hound dog of a Congressman zeroes in on her and sharpens the shady tools at his fingertips, including blackmail and murder.', 'tt0117765'),
(35, '682254', 'Scooby-Doo! The Sword and the Scoob', '2021', '2021-02-24', '72 min', '', 'Maxwell Atoms', 'Grey DeLisle, Stephen Stanton, Kari Wahlgren, Frank Welker, Kate Micucci', 'United States of America', '/sCoG0ibohbPrnyomtzegSuBL40L.jpg', 7.7, 'movie', '2023-09-04 14:45:43', '0000-00-00 00:00:00', 'An evil sorceress transports the gang back to the age of chivalrous knights, spell-casting wizards, and fire-breathing dragons.', 'tt13676256'),
(36, '79302323202', 'Dreamcadsadatcher', '2021', '2021-03-05', '90 min', '', 'Jacob Johnston', 'Niki Koss, Zachary Gordon, Travis Burns, Blaine Kern III, Adrienne Wilkinson', 'United States of America', '/3CF743g2BpC8r3mCVV9gSX2yQBj.jpg', 5.9, 'movie', '2023-09-06 16:47:32', '2023-09-06 16:47:32', 'The film centers on two estranged sisters who, along with their friends, become entrenched in a 48-hour whirlwind of violence after a traumatic experience at an underground music festival.', 'tt9382172'),
(39, '327194122004131', 'test movie', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-09-14 08:23:28', '2023-09-14 08:23:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `api_key`, `created_at`) VALUES
(1, 'adadsas@sddassa.com', 'kVTZcNzcpHtmhMl1GcL2fLTfGXAACPGQ', '2023-09-13 07:56:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
