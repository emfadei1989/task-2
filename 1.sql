CREATE TABLE posts (
  id int(10) NOT NULL AUTO_INCREMENT,
  user_id int(10) NOT NULL,
  category_id int(10) NOT NULL,
  content varchar(242) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/**
* Таблица пользователей
*/
CREATE TABLE users (
  id int(10) NOT NULL AUTO_INCREMENT,
  name varchar(32) NOT NULL,
  gender tinyint(2) NOT NULL,
  email varchar(1024) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
* Таблица категорий
*/
CREATE TABLE categories (
  id int(10) NOT NULL AUTO_INCREMENT,
  name varchar(20) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
* Таблица лайков
*/

CREATE TABLE `likes` (
  post_id int(10) NOT NULL,
  user_id int(10) NOT NULL,
  PRIMARY KEY (post_id,user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
* запрос на постановку лайка от юзера к новости
*/
INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id);

/**
* запрос на отмену лайка;
*/
DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id;

/**
* выборка пользователей, оценивших новость
*/

SELECT l.post_id, l.user_id, u.name FROM likes l
  LEFT JOIN users u ON u.id=l.user_id
WHERE l.post_id = :post_id
LIMIT :offset, :limit;

/**
* запрос для вывода ленты новостей;
*/

SELECT p.*, count_likes,u.name as user_created, c.name as category_name
FROM posts p
  LEFT JOIN users u on p.user_id = u.id
  LEFT JOIN categories c on p.category_id = c.id
  LEFT JOIN
    (SELECT post_id,count(post_id) as count_likes
     FROM likes   group by post_id
    ) sub
  ON sub.post_id = p.id
  WHERE c.name = :category_name
  LIMIT :offset, :limit;
  ;


/**
* запрос на добавление поста в ленту
*/
INSERT INTO posts (user_id,category_id, content) VALUES (:user_id, category_id, :content);
