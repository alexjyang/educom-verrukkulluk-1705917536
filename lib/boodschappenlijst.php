<?php

class boodschappen
{

    private $connection;

    private $ingredient;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->ingredient = new ingredient($connection);
    }

    private function selecteerIngredient($gerecht_id)
    {
        $ingredient = $this->ingredient->selecteerIngredient($gerecht_id);
        return $ingredient;
    }

    public function ophalenBoodschappen($user_id)
    {
        $boodschappen = [];

        $sql = "SELECT * FROM boodschappenlijst WHERE user_id = $user_id";
        $result = mysqli_query($this->connection, $sql);

        while ($row = mysqli_fetch_array($result)) {
            $boodschappen[] = $row;
        }

        return $boodschappen;
    }

    public function ArtikelOpLijst($artikel_id, $user_id)
    {
        $boodschappen = $this->ophalenBoodschappen($user_id);

        foreach ($boodschappen as $boodschap) {
            if ($boodschap['artikel_id'] == $artikel_id) {
                return $boodschap;
            }
        }
        return false;
    }

    public function artikelToevoegen($artikel_id, $user_id, $aantal)
    {

        $sql = "INSERT INTO boodschappenlijst (artikel_id, user_id, aantal)
        VALUES ($artikel_id, $user_id, $aantal)";

        $result = mysqli_query($this->connection, $sql);

        if ($result) {
            return true;
        }
        return false;
    }

    public function artikelBijwerken($artikel_id, $user_id, $aantal)
    {
        $sql = "UPDATE boodschappenlijst SET aantal = $aantal WHERE artikel_id= $artikel_id AND user_id = $user_id";
        $result = mysqli_query($this->connection, $sql);

        if ($result) {
            return true;
        }
        return false;
    }

    public function boodschappenToevoegen($gerecht_id, $user_id)
    {
        $ingredienten = $this->selecteerIngredient($gerecht_id);

        foreach ($ingredienten as $ingredient) {

            $artikel_id = $ingredient['artikel_id'];
            $aantal = $ingredient['aantal'];
            $boodschap = $this->ArtikelOpLijst($ingredient['artikel_id'], $user_id);

            if ($boodschap) {
                $this->artikelBijwerken($artikel_id, $user_id, $aantal);
            } else {
                $this->artikelToevoegen($artikel_id, $user_id, $aantal);
            }
        }
    }

    public function clearLijst()
    {
        $sql = "DELETE FROM boodschappenlijst";
        $result = mysqli_query($this->connection, $sql);

        if ($result) {
            echo "Cleared grocery list";
        }
    }
}