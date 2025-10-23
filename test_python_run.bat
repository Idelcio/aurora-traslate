@echo off
echo STARTING PYTHON WRAPPER
echo Working Directory: %CD%
echo Python: %1
echo Script: %2
echo Args: %*
echo.

%*

echo.
echo Exit Code: %ERRORLEVEL%
