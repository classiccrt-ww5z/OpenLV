<?php
class Videos {
    public      $WHERE_P            = false,
        $WHERE_C                    = "",
        $SELECT                     = "videos.*",
        $ORDER_BY                   = false,
        $STATUS                     = 2,
        $Private_Videos             = false,
        $Unlisted_Videos            = false,
        $Banned_Users               = false,
        $Shadowbanned_Users         = false,
        $LIMIT                      = 20,
        $JOIN                       = "",
        $Uploader                   = true,
        $Distinct                   = false,
        $Blocked                    = true,
        $Execute                    = [],
        $Count                      = false,
        $Group_By                   = "",
        $Racism                     = true,
        $Count_Column               = "videos.title";

    public static   $Videos,
                    $Amount;

    private         $DB,
                    $_USER,
                    $_LANG;


    function __construct(Database $DB, User $_USER) {
        $this->DB       = $DB;
        $this->_USER    = $_USER;
    }

    public function get() {
        if (!$this->Blocked) {
            $Username = $this->_USER->username;
            $Logged_In = $this->_USER->logged_in;
        }
if (!$this->Blocked && $Logged_In) {
    $this->Execute[':USERNAME1'] = $Username;
    $this->Execute[':USERNAME2'] = $Username;
}

        if (is_a($this->LIMIT,"Pagination")) { $LIMIT = "LIMIT ".$this->LIMIT->From.", ".$this->LIMIT->To; }
        elseif ($this->LIMIT === false)                { $LIMIT = ""; }
        else                                           { $LIMIT = "LIMIT ".$this->LIMIT; }

        $ORDER_BY = $this->ORDER_BY ? "ORDER BY $this->ORDER_BY" : "";
        $Distinct = $this->Distinct ? "DISTINCT" : "";

        $WHERE = "WHERE (videos.title IS NOT NULL AND videos.title != '' AND videos.banned_uploader = 0)";
        
        if ($this->STATUS === 2) { $WHERE .= " AND (videos.status = 2 OR videos.status IS NULL)"; }
        elseif ($this->STATUS === 1) { $WHERE .= " AND (videos.status = 1 OR videos.status IS NULL)"; }
        elseif ($this->STATUS === 0) { $WHERE .= " AND (videos.status = 0 OR videos.status IS NULL)"; }
        elseif ($this->STATUS === 3) { $WHERE .= " AND (videos.status IN (-2,0,1,2) OR videos.status IS NULL)"; }

        if ($this->Private_Videos === false) { $WHERE .= " AND (videos.privacy <> 1 OR videos.privacy IS NULL)"; }
        if ($this->Unlisted_Videos === false) { $WHERE .= " AND (videos.privacy <> 2 OR videos.privacy IS NULL)"; }
        if ($this->Shadowbanned_Users === false) { $WHERE .= " AND (videos.shadowbanned_uploader = 0 OR videos.shadowbanned_uploader IS NULL)"; }

        if ($this->Blocked === false && $Logged_In) { 
            $WHERE .= " AND (users_block.blocker IS NULL)"; 
        }

        if (!empty($WHERE) && $WHERE !== "WHERE " && $this->WHERE_P !== false) { $WHERE .= " AND "; }
        if ($this->WHERE_P !== false && is_array($this->WHERE_P) && count($this->WHERE_P) > 0) {
            $Amount = count($this->WHERE_P);
            $Count  = 0;
            foreach ($this->WHERE_P as $P => $Value) {
                $Count++;
                $WHERE .= "$P = :".str_replace(".","",$P);
                if ($Amount !== $Count) { $WHERE .= " AND "; }
                $this->Execute[":".str_replace(".","",$P)] = $Value;
            }
        }

        $Single = $this->LIMIT === "LIMIT 1";

        if ($this->Uploader) { 
            $this->SELECT .= ", users.displayname, users.partner, users.avatar"; 
            $this->JOIN .= " LEFT JOIN users ON videos.uploaded_by = users.username"; 
        }
        if ($this->Blocked === false && $Logged_In)  {  
            $this->SELECT .= ", users_block.blocker"; 
            $this->JOIN .= " LEFT JOIN users_block ON ((:USERNAME1 = users_block.blocker AND videos.uploaded_by = users_block.blocked) OR (:USERNAME2 = users_block.blocked AND videos.uploaded_by = users_block.blocker)) ";
        }

        if ($this->Count) { 
            $this->SELECT = "count($this->Count_Column) as amount"; 
            $Single = true; 
        }

        $Videos = $this->DB->execute("SELECT $Distinct $this->SELECT FROM videos $this->JOIN $WHERE $this->WHERE_C $this->Group_By $ORDER_BY $LIMIT",$Single,$this->Execute);

        static::$Amount = $this->DB->RowNum;

        if (!$this->Count) {
            if (static::$Amount > 0) {
                $this::$Videos = $Videos;
                return true;
            } else {
                static::$Videos = false;
            }
        } else {
            $this::$Amount = $Videos["amount"];
            return $Videos["amount"];
        }
        return false;
    }

    public function fixed(bool $Thumbnail = true) {
        if (!isset(static::$Videos)) { throw new Exception("errV.C.P"); }
        if (static::$Videos === false) { return false; }

        $filteredVideos = [];

        foreach(static::$Videos as $Video => $Value) {
            $Can_see = ($this->_USER->logged_in && $this->_USER->username === $Value["uploaded_by"]) || ($Value["privacy"] == 0);

            if (!$Can_see) {
                $Value["title"] = "Private Video";
                $Value["description"] = "This is a private video";
                $Value["tags"] = false;
                $Value["uploaded_by"] = "???";
                $Value["displayname"] = "???";
                $Value["views"] = 0;
                $Value["comments"] = 0;
                $Value["length"] = "";
                $Value["1_star"] = 0;
                $Value["2_star"] = 0;
                $Value["3_star"] = 0;
                $Value["4_star"] = 0;
                $Value["5_star"] = 0;
            } else {
                $Value["description"] = $Value["description"] ?? false;
                $Value["tags"] = $Value["tags"] ?? false;
                $Value["views"] = $Value["displayviews"] ?? 0;
                $Value["comments"] = $Value["comments"] ?? 0;
                if (isset($Value["length"])) {
                    $Value["seconds"] = $Value["length"];
                    $Value["length"] = seconds_to_time($Value["length"]);
                } else {
                    $Value["length"] = "";
                }
            }

            if (!isset($Value["url"]) || empty($Value["url"])) {
                $Value["url"] = "";
                $Value["1_star"] = 0;
                $Value["2_star"] = 0;
                $Value["3_star"] = 0;
                $Value["4_star"] = 0;
                $Value["5_star"] = 0;
            }

            if (!isset($Value["uploaded_on"]) || empty($Value["uploaded_on"])) {
                $Value["uploaded_on"] = "1997-09-15 14:07:00";
            }

            if ($Thumbnail) {
                if ($Can_see && isset($Value["url"]) && !empty($Value["url"]) && file_exists(ROOT_FOLDER."/usfi/thmp/".$Value["url"].".jpg")) {
                    $Image = "/usfi/thmp/".$Value["url"].".jpg";
                } else {
                    $Image = "/img/no_th.jpg";
                }
                $Value["thumbnail"] = 'src="'.$Image.'" alt="'.str_replace(['"', "'"], "", $Value["title"]).'" title="'.str_replace(['"', "'"], "", $Value["title"]).'"';
                $Value["thumbnail2"] = $Image;
            }

            $filteredVideos[] = $Value;
        }

        if ($this->LIMIT !== 1) {
            return $filteredVideos;
        } else {
            return $filteredVideos[0] ?? null;
        }
    }
}
