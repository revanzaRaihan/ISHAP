import React from "react";

interface CardProps extends React.HTMLAttributes<HTMLDivElement> {
  children: React.ReactNode;
}

export const Card: React.FC<CardProps> = ({ children, className = "", style, ...props }) => {
  return (
    <div className={`card ${className}`} style={style} {...props}>
      {children}
    </div>
  );
};
