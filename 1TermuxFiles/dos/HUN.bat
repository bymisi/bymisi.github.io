@echo off
    IF EXIST HUN.XPSETUP (
        echo Previous Setup Files are HUN
        GOTO done
    ) ELSE (
        echo Finding Previous Setup Files...
    )
    IF EXIST 2010.XPSETUP (
        echo Previous Setup Files are 2010
	ren $WIN_NT$.~LS 2010$WIN_NT$.~LS
	ren $WIN_NT$.~BT 2010$WIN_NT$.~BT
	ren TXTSETUP.SIF 2010TXTSETUP.SIF
	ren NTLDR 2010NTLDR
	ren XATSP DEFXATSP
	ren XPSTP DEFXPSTP
	ren menu.lst DEFmenu.lst

	ren HUN$WIN_NT$.~LS $WIN_NT$.~LS
	ren HUN$WIN_NT$.~BT $WIN_NT$.~BT
	ren HUNtxtsetup.sif txtsetup.sif
	ren HUNNTLDR NTLDR
	ren HUNXATSP XATSP
	ren HUNXPSTP XPSTP
	ren HUNmenu.lst menu.lst
	ren *.XPSETUP HUN.XPSETUP
        GOTO done
    ) ELSE (
        echo.
    )
    IF EXIST DEF.XPSETUP (
        echo Previous Setup Files are DEF
	ren $WIN_NT$.~LS DEF$WIN_NT$.~LS
	ren $WIN_NT$.~BT DEF$WIN_NT$.~BT
	ren TXTSETUP.SIF DEFTXTSETUP.SIF
	ren NTLDR DEFNTLDR
	ren XATSP DEFXATSP 
	ren XPSTP DEFXPSTP
	ren menu.lst DEFmenu.lst

	ren HUN$WIN_NT$.~LS $WIN_NT$.~LS
	ren HUN$WIN_NT$.~BT $WIN_NT$.~BT
	ren HUNtxtsetup.sif txtsetup.sif
	ren HUNNTLDR NTLDR
	ren HUNXATSP XATSP
	ren HUNXPSTP XPSTP
	ren HUNmenu.lst menu.lst
	ren *.XPSETUP HUN.XPSETUP
        GOTO done
    ) ELSE (
        echo.
    )
GOTO error
:done
echo DONE
pause
GOTO END
:error
echo FILE IS MiSSiNG!!!
pause