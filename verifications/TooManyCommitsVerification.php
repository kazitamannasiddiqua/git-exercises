<?php

class TooManyCommitsVerification extends AbstractVerification
{
    private static $hints = <<<HINTS
The easiest way to make one commit out of two (or more) is to squash them
with git rebase -i command and choose squash option for all but the first
commit you want to preserve. Note that you can also use fixup command
when you want to discard consequent commit messages and leave only the
first one.

Remember that you don't need to know the commit SHA-1 hashes when specifying
them in git rebase -i command. When you know that you want to go 2 commits
back, you can always run git rebase -i HEAD^^ or git rebase -i HEAD~2.

Note that you should not squash commits when you have published them already.
Need to know why? See: http://git-scm.com/book/en/v2/Git-Branching-Rebasing#The-Perils-of-Rebasing

For more info, see: http://git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Squashing-Commits
HINTS;


    public function getShortInfo()
    {
        return 'Make one commit out of two.';
    }

    protected function doVerify()
    {
        $commit = $this->ensureCommitsCount(1);
        $file = $this->ensureFilesCount($commit, 1);
        $this->ensure($file == 'file.txt', 'The file that has been commited does not look like the generated one.');
        $fileLines = $this->getFileContent($commit, 'file.txt');
        $this->ensure(count($fileLines) == 2, 'file.txt is supposed to have 2 lines after squash. Received %d.', [count($fileLines)]);
        $this->ensure($fileLines[0] == 'This is the first line.', 'Invalid first line in the file.');
        $this->ensure($fileLines[1] == 'This is the second line I have forgotten.', 'Invalid second line in the file.');
        $this->ensure(GitUtils::getCommitSubject($commit) == 'Add file.txt', 'You should leave commit message as it was in the first commit.');
        return self::$hints;
    }
}