#!/usr/bin/perl -w

while ( <STDIN> ) {
    my $line = $_;
    chomp ( $line ) ;
    for ( $line ) {
        s/\[debug.*/\e[1;44m$&\e[0m/gi;
        s/\[api->render.*/\e[1;03m$&\e[0m/gi;
        s/===.*/\e[1;25m$&\e[0m/gi;
        s/.*\[fatal.*/\e[0;31m$&\e[0m/gi;
        s/\[info.*|\[time].*/\e[1;30m$&\e[0m/gi;
        s/\[error.*/\e[0;31m$&\e[0m/gi;
        s/\[auto.*\]|\[render.*\]|rendering|success.*/\e[0;32m$&\e[0m/gi;
        s/.timing.*/\e[0;33m$&\e[0m/gi;
        s/\[warning.*/\e[0;33m$&\e[0m/gi;
    }
    print $line , "\n" ;
}
