<?php namespace SKYOJ;

if (!defined('IN_SKYOJSYSTEM')) {
    exit('Access denied');
}

class challenge
{
    private $uid;
    private $pid;
    private $suffix;
    private $id;

    public function __construct($uid, $pid, $code, $suffix)
    {
        $this->uid = $uid;
        $this->pid = $pid;
        $this->suffix = $suffix;
        $create = $this->create($uid, $pid, $code, $suffix);
        if ($create === false) {
            return false;
        } else {
            $this->id = $create;
        }
    }

    private function create($uid, $pid, $code, $suffix)
    {
        global $_E;
        $tchal = DB::tname('challenge');
        $sql = "INSERT INTO `$tchal` (`problem`,`user`,`suffix`) VALUES (?,?,?)";
        $pdo = DB::query($sql, [$pid, $uid, $suffix]);
        if (!$pdo) {
            LOG::msg(Level::Warning, 'mysql insert data error');

            return false;
        }
        $id = DB::lastInsertId();
        LOG::msg(Level::Debug, "$id");
        $codepath = $_E['challenge']['path'].'code/'.$id.'.'.$suffix;
        $codefile = @fopen($codepath, 'x');
        if ($codefile === false) {
            LOG::msg(Level::Warning, "cannot open the file $codepath");

            return false;
        }
        fwrite($codefile, $code);
        fclose($codefile);

        return $id;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_uid()
    {
        return $this->uid;
    }

    public function get_pid()
    {
        return $this->pid;
    }

    public function get_suffix()
    {
        return $this->suffix;
    }
}