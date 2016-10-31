<?php

declare(strict_types=1);
class co_func
{
    private $sql;
    private $user_id;
    private $detail;
    private $follow_id;
    private $price;
    private $course_id;
    public function __construct()
    {
        $this->sql = new PDO('mysql:dbname=robruu_online;host=127.0.0.1', 'root', '@PeNtesterMYSQL');
        $this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function comment(int $id_user, string $comment, string $id_playlist)
    {
        $sql = $this->sql->prepare('SELECT id_playlist FROM video_playlist WHERE id_playlist = :id_playlist ;');
        $sql->bindParam(':id_playlist', $id_video, PDO::PARAM_STR);
        $sql->execute();
        $fetch = $sql->fetch(PDO::FETCH_ASSOC);
        if ($fetch['id_playlist'] == $id_video) {
            $sql = $this->sql->prepare('SELECT id_post,COUNT(id_post) FROM comment WHERE id_post = :id_post ;');
            $sql->bindParam(':id_post', $id_video, PDO::PARAM_STR);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);
            if ($fetch) {
                $id_N = $fetch['COUNT(id_post)'] + 1;
                $sql = $this->sql->prepare('INSERT INTO comment(id_post,id_N,comment,time,id_user)
                                            VALUES (:id_post ,:id_N ,:comment ,:time , :id_user)');
                $sql->bindParam(':id_post', $id_playlist, PDO::PARAM_STR);
                $sql->bindParam(':id_N', $id_N, PDO::PARAM_INT);
                $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql->bindParam(':time', date('D / m / Y'), PDO::PARAM_STR);
                $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                $sql->execute();

                return 'complete';
            } else {
                return 'error';
            }
        }
    }
    public function rating(int $id_user, string $id_question = null, string $id_playlist = null)
    {
        if ($id_question != null) {
            $sql = $this->sql->prepare('SELECT id FROM picture WHERE id = :id ; ');
            $sql->bindParam(':id', $id_question, PDO::PARAM_INT);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);
            if ($fetch) {
                $sql = $this->sql->prepare('SELECT * FROM check_rating WHERE id_post = :id_question
                                            AND type = 1 AND id_user = :id_user ; ');
                $sql->bindParam(':id_question', $id_question, PDO::PARAM_INT);
                $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                $sql->execute();
                $fetch = $sql->fetch(PDO::FETCH_ASSOC);
                if (!$fetch) {
                    $sql = $this->sql->prepare('SELECT num FROM rating WHERE id_post = :id_question AND type = 1;');
                    $sql->bindParam(':id_question', $id_question, PDO::PARAM_INT);
                    $sql->execute();
                    $fetch1 = $sql->fetch(PDO::FETCH_ASSOC);
                    if ($fetch1) {
                        $sql = $this->sql->prepare('UPDATE rating SET num = num + 1 WHERE id_post = :id_post AND type = 1;');
                        $sql->bindParam(':id_post', $id_question, PDO::PARAM_INT);
                        $sql->execute();
                        $sql = $this->sql->prepare('INSERT INTO check_rating(id_user,id_post,type)
                                                VALUES (:id_user ,:id_post ,1);');
                        $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                        $sql->bindParam(':id_post', $id_question, PDO::PARAM_INT);
                        $sql->execute();
                        return (array) $fetch1;
                    } else {
                        $sql = $this->sql->prepare('INSERT INTO rating(id_post,type,num)
                                                    VALUES(:id_post ,1,1)');
                        $sql->bindParam(':id_post', $id_question, PDO::PARAM_INT);
                        $sql->execute();
                        $sql = $this->sql->prepare('INSERT INTO check_rating(id_user,id_post,type)
                                              VALUES (:id_user ,:id_post ,1);');
                        $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                        $sql->bindParam(':id_post', $id_question, PDO::PARAM_INT);
                        $sql->execute();
                        return (array) $fetch1;
                    }
                } else {
                    return 'error';
                }
            } else {
                return 'error';
            }
        } elseif ($id_playlist != null) {
            $sql = $this->sql->prepare('SELECT id FROM video_playlist WHERE id_playlist = :id ;');
            $sql->bindParam(':id', $id_playlist, PDO::PARAM_STR);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);
            if ($fetch) {
                $sql = $this->sql->prepare('SELECT * FROM check_rating WHERE id_post = :id_playlist
                                            AND type = 2 AND id_user = :id_user ; ');
                $sql->bindParam(':id_playlist', $id_playlist, PDO::PARAM_STR);
                $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                $sql->execute();
                $fetch = $sql->fetch(PDO::FETCH_ASSOC);
                if (!$fetch) {
                    $sql = $this->sql->prepare('SELECT num FROM rating WHERE id_post = :id_playlist
                                                AND type = 2;');
                    $sql->bindParam(':id_playlist', $id_playlist, PDO::PARAM_STR);
                    $sql->execute();
                    $fetch1 = $sql->fetch(PDO::FETCH_ASSOC);
                    if ($fetch1) {
                        $sql = $this->sql->prepare('UPDATE rating SET num = num + 1 WHERE id_post = :id_post AND type = 2;');
                        $sql->bindParam(':id_post', $id_playlist, PDO::PARAM_STR);
                        $sql->execute();
                        $sql = $this->sql->prepare('INSERT INTO check_rating(id_user,id_post,type)
                                                VALUES (:id_user ,:id_post ,2);');
                        $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                        $sql->bindParam(':id_post', $id_playlist, PDO::PARAM_STR);
                        $sql->execute();

                        return (array) $fetch1;
                    } else {
                        $sql = $this->sql->prepare('INSERT INTO rating(id_post,type,num)
                                                    VALUES(:id_post ,2,1)');
                        $sql->bindParam(':id_post', $id_playlist, PDO::PARAM_STR);
                        $sql->execute();
                        $sql = $this->sql->prepare('INSERT INTO check_rating(id_user,id_post,type)
                                              VALUES (:id_user ,:id_post ,2);');
                        $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                        $sql->bindParam(':id_post', $id_playlist, PDO::PARAM_STR);
                        $sql->execute();

                        return (array) $fetch1;
                    }
                } else {
                    return 'error';
                }
            } else {
                return 'error';
            }
        }
    }
    public function search(int $type, string $detail)
    {
        $return = array();
        $detail = '%'.$detail.'%';
        if ($type == 1) {
            $course = $this->sql->prepare('SELECT course_name,price,id_author
                                           FROM video_playlist WHERE course_name
                                           LIKE :detail ; ');
            $question = $this->sql->prepare('SELECT name,num_question FROM question_playlist
                                             WHERE name LIKE :detail ; ');
            $question->bindParam(':detail', $detail, PDO::PARAM_STR);
            $question->execute();
            $question_f = $question->fetchAll();
            $course->bindParam(':detail', $detail, PDO::PARAM_STR);
            $course->execute();
            $course_f = $course->fetchAll(PDO::FETCH_ASSOC);

            return (array) array_merge($course_f, $question_f);
        } elseif ($type == 2) {
            $intructor = $this->sql->prepare('SELECT *
                                         FROM video_playlist WHERE course_name
                                         LIKE :detail ; ');
            $intructor->bindParam(':detail', $detail, PDO::PARAM_STR);
            $intructor->execute();

            return (array) $intructor->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($type == 3) {
            $course = $this->sql->prepare('SELECT id_author
                                       FROM video_playlist WHERE course_name
                                       LIKE :detail ; ');
            $course->bindParam(':detail', $detail, PDO::PARAM_STR);
            $course->execute();

            return (array) $course->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    public function buy(string $id_course, int $id_user)
    {
        $sql = $this->sql->prepare('SELECT money FROM user WHERE id = :user_id');
        $sql->bindParam(':user_id', $id_user, PDO::PARAM_INT);
        $sql->execute();
        $fetch = $sql->fetch(PDO::FETCH_ASSOC);
        if ($fetch) {
            $sql = $this->sql->prepare('SELECT price,flag_num FROM video_playlist WHERE id_playlist = :id AND flag_num=1;');
            $sql->bindParam(':id', $id_course, PDO::PARAM_INT);
            $sql->execute();
            $fetch1 = $sql->fetch(PDO::FETCH_ASSOC);
            if ($fetch1 == true && $fetch1['flag_num'] == 1) {
                $sql = $this->sql->prepare('SELECT * FROM course_user WHERE user_id = :id_user AND course_id=:course_id ;');
                $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                $sql->bindParam(':course_id', $id_course, PDO::PARAM_STR);
                $sql->execute();
                $fetch3 = $sql->fetch(PDO::FETCH_ASSOC);
                if (!$fetch3) {
                    if ($fetch['money'] >= $fetch1['price']) {
                        $sql = $this->sql->prepare('INSERT INTO course_user(user_id,course_id)
                                                VALUES (:id_user ,:id_course ) ;');
                        $sql->bindParam(':id_user', $id_user);
                        $sql->bindParam(':id_course', $id_course);
                        $sql->execute();
                        $sql = $this->sql->prepare('UPDATE user SET
                                                money = money - :price WHERE id = :id_user ; ');
                        $sql->bindParam(':price', $fetch1['price'], PDO::PARAM_INT);
                        $sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                        $sql->execute();

                        return 'complete';
                    } else {
                        return 'not_enough_money';
                    }
                } else {
                    return 'have_course';
                }
            } else {
                return 'error';
            }
        } else {
            return 'not_login';
        }
    }
}
?>
